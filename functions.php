<?php

require_once('exceptions/ValidationException.php');
require_once('interfaces/Validate.php');
require_once('classes/Microwave.php');
require_once('classes/MicrowaveProgram.php');
require_once('classes/Timer.php');
require_once('classes/Potency.php');

function benner_setup_theme(){
	global $wpdb;
	// $wpdb->query('DROP TABLE benner_microwave_programs');
	$q = $wpdb->query('SELECT * 
		FROM information_schema.tables
		WHERE table_name = "benner_microwave_programs"
		LIMIT 1;');
	if(empty($q)) {
		$sqlCreateTable = 'CREATE TABLE benner_microwave_programs (
			id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
			slug VARCHAR(60) NOT NULL,
			title VARCHAR(60) NOT NULL,
			food_description VARCHAR(255) NOT NULL,
			timer INT NOT NULL,
			potency TINYINT NOT NULL,
			instructions TEXT,
			custom_heating_character VARCHAR(1) NOT NULL,
			is_default_program BOOL NOT NULL
		)';
		$r = $wpdb->query($sqlCreateTable);

		$wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title('Pipoca'),
			'title' => 'Pipoca',
			'food_description' => 'Pipoca (de micro-ondas)',
			'timer' => 180,
			'potency' => 7,
			'instructions' => 'Observar o barulho de estouros do milho, caso houver um intervalo de mais de 10 segundos entre um estouro e outro, interrompa o aquecimento.',
			'custom_heating_character' => '*',
			'is_default_program' => true
		]);

		$wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title('Leite'),
			'title' => 'Leite',
			'food_description' => 'Leite',
			'timer' => 300,
			'potency' => 5,
			'instructions' => 'Cuidado com aquecimento de líquidos, o choque térmico aliado ao movimento do recipiente pode causar fervura imediata causando risco de queimaduras.',
			'custom_heating_character' => '@',
			'is_default_program' => true
		]);

		$wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title('Carne de boi'),
			'title' => 'Carne de boi',
			'food_description' => 'Carne em pedaço ou fatias',
			'timer' => 840,
			'potency' => 4,
			'instructions' => 'Interrompa o processo na metade e vire o conteúdo com a parte de baixo para cima para o descongelamento uniforme.',
			'custom_heating_character' => '#',
			'is_default_program' => true
		]);

		$wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title('Frango'),
			'title' => 'Frango',
			'food_description' => 'Frango (qualquer corte)',
			'timer' => 480,
			'potency' => 7,
			'instructions' => 'Interrompa o processo na metade e vire o conteúdo com a parte de baixo para cima para o descongelamento uniforme.',
			'custom_heating_character' => '*',
			'is_default_program' => true
		]);

		$wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title('Feijão'),
			'title' => 'Feijão',
			'food_description' => 'Feijão congelado',
			'timer' => 480,
			'potency' => 8,
			'instructions' => 'Deixe o recipiente destampado e em casos de plástico, cuidado ao retirar o recipiente pois o mesmo pode perder resistência em altas temperaturas.',
			'custom_heating_character' => '°',
			'is_default_program' => true
		]);
	}
}
add_action('after_setup_theme', 'benner_setup_theme');

function add_theme_css_js(){
	wp_enqueue_style( 'style', get_stylesheet_uri() );
	// wp_enqueue_script( 'script', get_template_directory_uri() . '/js/jquery-3.7.1.min.js', array( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'jquery_mask', get_template_directory_uri() . '/js/jquery.mask.min.js', array( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), 1.1, true);
}
add_action('wp_enqueue_scripts', 'add_theme_css_js');

function microondas_post() {
	$r = [
		'error' => false,
		'alert' => false,
		'action' => false,
		'args' => false,
	];
	try {
		if(($timer = $_POST['timer']) === '') {
			$timer = 30;
		}
		if(($potencia = $_POST['potencia']) === '') {
			$potencia = 10;
		}
		$microwave = new Microwave(intval($timer), intval($potencia));
		if($microwave->validate()) {
			$r['action'] = 'microwave_start';
			$r['args'] = [
				'formatted_timer' => $microwave->getTimer()->getFormattedTime(),
				'timer' => $microwave->getTimer()->getTime(),
				'potency_factor' => $microwave->getPotency()->getFactor(),
			];
		}
	} catch (ValidationException $e) {
		$r['error'] = true;
		$r['alert'] = $e->getMessage();
	} catch (Exception $e) {
		$r['error'] = true;
		$r['alert'] = 'Um erro inesperado ocorreu, tente novamente mais tarde!';
	}
	wp_send_json($r);
}
add_action('wp_ajax_microondas_post', 'microondas_post');
add_action('wp_ajax_nopriv_microondas_post', 'microondas_post');

function getAllMicrowavePrograms() {
	global $wpdb;
	$results = $wpdb->get_results('SELECT * FROM benner_microwave_programs ORDER BY id');
	$r = [];
	foreach($results as $row) {
		$p = new MicrowaveProgram($row->title, $row->food_description, $row->timer, $row->potency, $row->instructions, $row->custom_heating_character, $row->is_default_program);
		$r[$p->getSanitizedTitle()] = $p;
	}
	return $r;
}

function microondas_program_post() {
	$programas = getAllMicrowavePrograms();
	$r = [
		'error' => false,
		'alert' => false,
		'action' => false,
		'args' => false,
	];
	try {
		if(!in_array($_POST['programa'], array_keys($programas))) {
			throw new ValidationException('O programa selecionado é inválido!');
		}
		$programa = $programas[$_POST['programa']];
		$r['action'] = 'microwave_program_setup';
		$r['args'] = [
			'formatted_timer' => $programa->getTimer()->getFormattedTime(),
			'timer' => $programa->getTimer()->getTime(),
			'potency_factor' => $programa->getPotency()->getFactor(),
			'custom_character' => $programa->getCustomHeatingCharacter(),
		];
	} catch (ValidationException $e) {
		$r['error'] = true;
		$r['alert'] = $e->getMessage();
	} catch (\Exception $e) {
		$r['error'] = true;
		$r['alert'] = 'Um erro inesperado ocorreu, tente novamente mais tarde!';
	}
	wp_send_json($r);
}
add_action('wp_ajax_microondas_program_post', 'microondas_program_post');
add_action('wp_ajax_nopriv_microondas_program_post', 'microondas_program_post');

function microondas_new_program_post() {
	global $wpdb;
	$r = [
		'error' => false,
		'alert' => false,
		'action' => false,
		'args' => false,
	];
	try {
		if(empty($title = $_POST['title'])) {
			throw new ValidationException('O campo Nome é obrigatório!');
		} else {
			$r = $wpdb->get_row($wpdb->prepare('SELECT * FROM benner_microwave_programs WHERE slug = %s', sanitize_title($_POST['title'])));
			if(!empty($r)) {
				throw new ValidationException('Já existe um programa com o Nome informado!');
			}
		}

		if(empty($food_description = $_POST['food_description'])) {
			throw new ValidationException('O campo Alimento é obrigatório!');
		}

		if(empty($timer = $_POST['timer'])) {
			throw new ValidationException('O campo Tempo é obrigatório!');
		} else if(!is_numeric($timer)) {
			throw new ValidationException('O campo Tempo deve ser um número!');
		} else if(!is_int($timer = intval($timer))){
			throw new ValidationException('O campo Tempo deve ser um número inteiro!');
		}

		if(empty($potency = $_POST['potency'])) {
			throw new ValidationException('O campo Potência é obrigatório!');
		} else if(!is_numeric($potency)) {
			throw new ValidationException('O campo Potência deve ser um número!');
		} else if(!is_int($potency = intval($potency))) {
			throw new ValidationException('O campo Potência deve ser um número inteiro!');
		}

		if(empty($custom_heating_character = $_POST['custom_heating_character'])) {
			throw new ValidationException('O campo Caráctere customizado é obrigatório!');
		} else if (strlen($custom_heating_character) > 1){
			throw new ValidationException('O campo Caráctere customizado deve conter apenas 1 caráctere!');
		} else {
			$r = $wpdb->get_row($wpdb->prepare('SELECT * FROM benner_microwave_programs WHERE custom_heating_character = %s', $_POST['custom_heating_character']));
			if(!empty($r)) {
				throw new ValidationException('Já existe um programa com o Caractere customizado informado!');
			}
		}

		$result = $wpdb->insert('benner_microwave_programs', [
			'slug' => sanitize_title($title),
			'title' => $title,
			'food_description' => $food_description,
			'timer' => $timer,
			'potency' => $potency,
			'instructions' => (!empty($instructions = $_POST['instructions']) ? $instructions : null),
			'custom_heating_character' => $custom_heating_character,
			'is_default_program' => false
		]);
		$r['action'] = 'microwave_new_program_register';
	} catch (ValidationException $e) {
		$r['error'] = true;
		$r['alert'] = $e->getMessage();
	} catch (\Exception $e) {
		$r['error'] = true;
		$r['alert'] = 'Um erro inesperado ocorreu, tente novamente mais tarde!';
	}
	wp_send_json($r);
}
add_action('wp_ajax_microondas_new_program_post', 'microondas_new_program_post');
add_action('wp_ajax_nopriv_microondas_new_program_post', 'microondas_new_program_post');
