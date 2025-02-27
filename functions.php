<?php

wp_enqueue_style( 'style', get_stylesheet_uri() );
// wp_enqueue_script( 'script', get_template_directory_uri() . '/js/jquery-3.7.1.min.js', array( 'jquery' ), 1.1, true);
wp_enqueue_script( 'jquery_mask', get_template_directory_uri() . '/js/jquery.mask.min.js', array( 'jquery' ), 1.1, true);
wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), 1.1, true);


function microondas_post() {
	echo "<PRE>";
	var_dump($_POST);
	echo "</PRE>";
	exit;
}
add_action('wp_ajax_microondas_post', 'microondas_post');
add_action('wp_ajax_nopriv_microondas_post', 'microondas_post');
