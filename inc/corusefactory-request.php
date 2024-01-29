<?php
/**
 * Path: wp-content/plugins/coursefac-integration/inc/corusefactory-request.php
 * Este archivo contiene el Las funciones para hacer peticiones a la API de CoureFactory.
 * @package Course Factory Integration 
 *
 * Las funciones que estan en este archivo nos permitiran:
 *  - Obtener el contenido de un ítem de cursos factory mediate su content_id.
 *  - Obtener una lista de todos los proyectos asociados a una cuenta de coursefactory que esta haciendo la peticion.
 *  - Obtener una lista de todas las versiones de un proyecto mediate su proyect_id.
 *  - Obtener una version de un proyecto mediate su proyect_id y su version_id. */


/**
 * Esta funcion obtiene el contenido de un item de course factory una vez suministrado su content_id.
 *
 * @param string $api_key
 * @param int $content_id
 * @return mixed
 */
function cfac_get_content( $api_key, $content_id ) {

	$req = wp_remote_get(
		"https://cob.coursefactory.net/outline-builder/api/public/content/version/$content_id",
		array(
			'headers' => array(
				'Authorization' => "Token $api_key",
			),
		)
	);

	$res = wp_remote_retrieve_body( $req );

	return $res;
}

/**
 * Esta function obtiene una lista de todos los proyectos asociados a una cuenta de coursefactory que esta haciendo la peticion.
 *
 * @param string $api_key
 * @return mixed
 */
function cfac_get_list_proyects( $api_key ) {

	$req = wp_remote_get(
		'https://cob.coursefactory.net/outline-builder/api/public/project/',
		array(
			'headers' => array(
				'Authorization' => "Token $api_key",
			),
		)
	);

	$res = wp_remote_retrieve_body( $req );

	return $res;
}

/**
 * Esta función obtiene una versión específica de un proyecto mediate su proyect_id y su version_id.
 * Retorna un objeto JSON con la información de la versión del proyecto.
 *
 * @param string $api_key
 * @param int $proyect_id
 * @param int $version_id
 * @return mixed
 */
function cfac_get_proyect_version( $api_key, $proyect_id, $version_id ) {

	$req = wp_remote_get(
		"https://cob.coursefactory.net/outline-builder/api/public/project/$proyect_id/version/$version_id",
		array(
			'headers' => array(
				'Authorization' => "Token $api_key",
			),
		)
	);

	$res = wp_remote_retrieve_body( $req );

	return $res;
}

/**
 * Esta función obtiene una lista de versiones de un proyecto mediante su proyect_id.
 *
 * @param string $api_key
 * @param int $proyect_id
 * @return mixed
 */
function cfac_get_proyect_versions( $api_key, $proyect_id ) {

	$req = wp_remote_get(
		"https://cob.coursefactory.net/outline-builder/api/public/project/$proyect_id/version/",
		array(
			'headers' => array(
				'Authorization' => "Token $api_key",
			),
		)
	);

	$res = wp_remote_retrieve_body( $req );

	return $res;
}

/**
 * Esta funcion obtiene el contenido de un item de course factory una vez suministrado su content_version_id.
 *
 * @param string $api_key
 * @param int $content_version_id
 * @return mixed
 */
function cfac_get_content_version( $api_key, $content_version_id ) {

	$req = wp_remote_get(
		"https://cob.coursefactory.net/outline-builder/api/public/content/version/$content_version_id",
		array(
			'headers' => array(
				'Authorization' => "Token $api_key",
			),
		)
	);

	error_log( print_r( 'cfac_get_content_version req', true ) );
	error_log( print_r( $req, true ) );

	$res = wp_remote_retrieve_body( $req );

	return $res;
}
