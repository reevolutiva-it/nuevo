<?php

/**
 * Plugin Name: CourseFactory LearnDash Integration
 * Description: A integration CourseFactory Course creator.
 * Author: Reevolutiva
 * Author URI: https://reevolutiva.cl
 * License: GPLv2
 * Version: 0.0.1
 * Text Domain: coursefactory_integration
 */


add_action( 'admin_menu', 'course_factory_integration_init_menu' );

/**
 * Init Admin Menu.
 *
 * @return void
 */
function course_factory_integration_init_menu() {

	$icon_url = plugin_dir_url( __FILE__ ) . '/js/public/Logo.png';

	add_menu_page( __( 
		'Course Factory', 'course_factory_integration' ), 
	__( 'Course Factory', 'course_factory_integration' ), 
		'manage_options', 'course_factory_integration', 
		'course_factory_integration_admin_page', 
		$icon_url, 
		'2.1' 
	);

}

/**
 * Init Admin Page.
 *
 * @return void
 */
function course_factory_integration_admin_page() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/backend.php';
}

/**
 * Esta funcion retorna la cookie de login de wordpress.
 *
 * @return void
 */
function get_wp_cookie() {
	// Obtnego cookie de la API.
	$cookie = array_filter(
		$_COOKIE,
		function ( $key ) {
			return strpos( $key, 'wordpress_logged_in_' ) !== false;
		},
		ARRAY_FILTER_USE_KEY
	);

	$logged_cookie = wp_json_encode( $cookie );
	$logged_cookie = str_replace( array( ':', '"', '}', '{' ), '', $logged_cookie );

	return $logged_cookie;
}


// Cuando este plugin se active.
register_activation_hook( __FILE__, 'course_factory_integration_activation' );

/**
 * Cuando este plugin se active.
 *
 * @return void
 */
function course_factory_integration_activation() {

	// Verfica si learnDash esta instalado.

	$plugins_active = get_option( 'active_plugins' );

	if ( ! in_array( 'sfwd-lms/sfwd_lms.php', $plugins_active ) ) {
		wp_die( 'LearnDash is not installed', 'LearnDash is not installed', array( 'back_link' => true ));
	}

	global $wpdb;

	// obtener learndash_settings_lessons_cpt de wp_options.
	$learndash_settings_lessons_cpt = get_option( 'learndash_settings_topics_cpt' );

	$supports = $learndash_settings_lessons_cpt['supports'];

	// en el array $supports, existe el string "comments", si no existe, entonces añadelo.
	if ( ! in_array( 'comments', $supports ) ) {
		array_push( $supports, 'comments' );

		$nuevo             = $learndash_settings_lessons_cpt;
		$nuevo['supports'] = $supports;

		$query = $wpdb->prepare( 'UPDATE wp_options SET option_value = %s WHERE option_name = %s', serialize( $nuevo ), 'learndash_settings_topics_cpt' );

		$wpdb->query( $query );
	}
}

require 'i18n/backend_i18n.php';

require 'inc/course-importer.php';

require 'inc/class/CFact_LD_Section_Heading.php';

require 'inc/corusefactory-request.php';

require 'inc/api-key-mannager.php';

require 'inc/endpoints.php';

require 'inc/custom-fields.php';

require 'inc/shortcode.php';

require 'inc/course-deleter.php';

require 'vite-load.php';
