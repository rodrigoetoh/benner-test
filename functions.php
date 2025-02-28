<?php

require_once('exceptions/ValidationException.php');
require_once('interfaces/Validate.php');
require_once('classes/Microwave.php');
require_once('classes/Timer.php');
require_once('classes/Potency.php');

wp_enqueue_style( 'style', get_stylesheet_uri() );
// wp_enqueue_script( 'script', get_template_directory_uri() . '/js/jquery-3.7.1.min.js', array( 'jquery' ), 1.1, true);
wp_enqueue_script( 'jquery_mask', get_template_directory_uri() . '/js/jquery.mask.min.js', array( 'jquery' ), 1.1, true);
wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), 1.1, true);


function microondas_post() {
	$r = [
		'error' => false,
		'alert' => false,
		'action' => false,
		'args' => false,
		'reset' => false,
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
