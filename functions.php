<?php

require_once('exceptions/ValidationException.php');
require_once('interfaces/Validate.php');
require_once('classes/Microwave.php');
require_once('classes/MicrowaveProgram.php');
require_once('classes/Timer.php');
require_once('classes/Potency.php');


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

$programPipoca = new MicrowaveProgram('Pipoca', 'Pipoca (de micro-ondas)', 180, 7, 'Observar o barulho de estouros do milho, caso houver um intervalo de mais de 10 segundos entre um estouro e outro, interrompa o aquecimento.', '*');
$programLeite = new MicrowaveProgram('Leite', 'Leite', 300, 5, 'Cuidado com aquecimento de líquidos, o choque térmico aliado ao movimento do recipiente pode causar fervura imediata causando risco de queimaduras.', '@');
$programCarne = new MicrowaveProgram('Carne de boi', 'Carne em pedaço ou fatias', 840, 4, 'Interrompa o processo na metade e vire o conteúdo com a parte de baixo para cima para o descongelamento uniforme.', '#');
$programFrango = new MicrowaveProgram('Frango', 'Frango (qualquer corte)', 480, 7, 'Interrompa o processo na metade e vire o conteúdo com a parte de baixo para cima para o descongelamento uniforme.', '*');
$programFeijao = new MicrowaveProgram('Feijão', 'Feijão congelado', 480, 8, 'Deixe o recipiente destampado e em casos de plástico, cuidado ao retirar o recipiente pois o mesmo pode perder resistência em altas temperaturas.', '°');
$programasPreDefinidos = [
	$programPipoca->getSanitizedTitle() => $programPipoca,
	$programLeite->getSanitizedTitle() => $programLeite,
	$programCarne->getSanitizedTitle() => $programCarne,
	$programFrango->getSanitizedTitle() => $programFrango,
	$programFeijao->getSanitizedTitle() => $programFeijao,
];

function microondas_program_post() {
	global $programasPreDefinidos;
	$r = [
		'error' => false,
		'alert' => false,
		'action' => false,
		'args' => false,
	];
	try {
		if(!in_array($_POST['programa'], array_keys($programasPreDefinidos))) {
			throw new ValidationException('O programa selecionado é inválido!');
		}
		$programa = $programasPreDefinidos[$_POST['programa']];
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
