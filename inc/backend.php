<?php

/**
 *  Path: wp-content/plugins/coursefac-integration/inc/backend.php.
 *  Este archivo contiene el codigo de la pagina de admistracion de coursefactory.
 *
 * @package Course Factory Integration
 */

$cookie = get_wp_cookie();


$api_key = cfact_ld_api_key_mannger( 'get' );

$backend_i18n = cousefact_backend_i18n();
echo '<script>const bakendi18n = ' . json_encode( $backend_i18n ) . ';</script>';
echo '<script> let req_project_list = false;</script>';
echo '<script> let cfact_learndash_integration_apiKey = "' . $api_key . '";</script>';

?>

<script>
	const wpApiCookie = "<?php echo $cookie; ?>";
</script>
<div id="my-back"></div>

<?php

if ( isset( $_GET['delete-api_key'] ) ) {

	cfact_ld_api_key_mannger( 'delete' );

	?>

	<script>
		location.href = `${location.origin}/wp-admin/admin.php?page=course_factory_integration`;
	</script>

	<?php

	die();
}

if ( $api_key ) :


	// Listamos todos los proyectos.

	$req_proyects = cfac_get_list_proyects( $api_key );
	$req_proyects = json_decode( $req_proyects );

	$proyectos = array();

	// Obtenemos el id de cada proyecto y mediante ese ID Listamos todas las verciones de ese proyecto.
	if ( $req_proyects->data ) {

		$proyects = $req_proyects->data;



		/**
		 * Buscamos en wp_postmeta un post_id que tenga el meta_key = cfact_project_version_id y el meta_value = id del proyecto.
		 * Si lo encontramos es que hay un curso importado con ese proyecto. y si no lo encontramos es que no hay un curso importado con ese proyecto.
		 */

		$proyectos = array_map(
			function ( $e ) {

				// Obtenemos el id del proyecto de CourseFactory.
				$id = $e->id;

				global $wpdb;

				// Construimos la peticion SQL.
				$query = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'cfact_project_version_id' AND meta_value = %s", $id );

				// Ejecutamos la peticion SQL.
				$post_id = (int) $wpdb->get_var( $query );

				// Si el post_id es vacio es que no hay un curso importado con ese proyecto.
				$exits = $post_id === 0 ? false : true;

				error_log( print_r( $post_id, true ) );
				error_log( print_r( $exits, true ) );

				if ( $exits ) {
					$e->exist   = 'true';
					$e->post_id = $post_id;
				} else {
					$e->exist = 'false';
				}

				// Retornamos el objeto con la propiedad exist.
				return $e;
			},
			$proyects
		);


		echo '<script>req_project_list = ' . json_encode( $proyects ) . ';</script>';
	}

	?>

	<?php
endif;

if ( ! $api_key ) :

	if ( isset( $_GET['set-api_key'] ) ) {

		$api_key_data = sanitize_text_field( $_GET['api-key'] );
		cfact_ld_api_key_mannger( 'add', $api_key_data );



		?>

		<script>
			location.href = `${location.origin}/wp-admin/admin.php?page=course_factory_integration`;
		</script>

		<?php

	}
endif;
