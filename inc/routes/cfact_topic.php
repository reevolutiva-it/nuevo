<?php
/**
 * Path: wp-content/plugins/coursefac-integration/inc/routes/cfact_curso.php
 * En este archivo se registran las rutas para configurar el custom-fiel de topic */
 

// Aqui se registra la ruta para añadir metadata de Coursefactory a un tema previamente importado desde CoruseFactory a LearnDash.
add_action('rest_api_init', function () {
    register_rest_route('cfact/v1', 'cfact-tema-cf', array(
        'methods' => 'POST',
        'callback' => 'cfact_tema_cf_callback',
        'permission_callback' => function () {
            return current_user_can( 'read' );
          }
    ));
});

/**
 * Esta funcion es el callback de la ruta cfact-tema-cf y se utiliza para añadir metadata de Coursefactory a un tema previamente importado desde CoruseFactory a LearnDash.
 *
 * @param WP_REST_Request $request
 * @return mixed
 */
function cfact_tema_cf_callback($request) {

    // Obtego el body de la petición.
    $body = $request->get_body();

    // Decodifico el body de la petición.
    $body = json_decode($body, true);

    //error_log(print_r($body, true));

    

    // Extraigo el topic_type del body de la petición.
    $topic_type = $body["topic_type"];

    // Extraigo el id del body de la petición.
    $id = $body["id"];

    // Actualizo cfact_topic_type con el valor de topic_type.
    if(!update_post_meta($id, 'cfact_topic_type', $topic_type)){
        // Si no existe el meta_key cfact_topic_type lo creo.
        add_post_meta($id, 'cfact_topic_type', $topic_type, true);
    }
    

    return $body;

}

