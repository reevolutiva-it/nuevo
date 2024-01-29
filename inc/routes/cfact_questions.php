<?php

/**
 * Path: wp-content/plugins/coursefac-integration/inc/routes/cfact_questions.php
 * Aqui se registra la ruta para crear preuntaas en LearnDash desde la api de Wordpress. */


 // Aqui se registra el endpoint cfact-questions para crear preguntas en LearnDash desde la api de Wordpress.
add_action('rest_api_init', function () {
    register_rest_route('cfact/v1', 'cfact-questions', array(
        'methods' => 'POST',
        'callback' => 'cfact_questions_callback',
        'permission_callback' => function () {
            return current_user_can( 'read' );
          }
    ));
});

/**
 * Esta funcion es el callback de la ruta cfact-questions y se utiliza para crear preguntas en LearnDash desde la api de Wordpress.
 *
 * @param WP_REST_Request $request
 * @return array
 */
function cfact_questions_callback($request){

    //$cookie = get_wp_cookie();

    // Obtener el body de la peticion.
    $body = $request->get_body();

    // Decodificar el body de la peticion.
    $body = json_decode($body, true);

    // Extraigo del body los parametros: post_title, post_content, post_ID, question_id.
    $post_title = $body["post_title"];
    $post_content = $body["post_content"];
    $quiz_id = $body["quiz_id"];
    $nonce = $body["nonce"];
    $answer = $body["answer"];
    $cookie = $body["cookie"];

    // STEP 1 - Crear la pregunta en Wordpress.

    $cpt_question_id = wp_insert_post([
        "post_title" => $post_title,
        "post_content" => $post_content,
        "post_type" => "sfwd-question",
        "post_status" => "publish"
    ]);

    // STEP 2 - Crear la pregunta en LearDash.

    $new_question_id = learndash_update_pro_question(0, [
        "post_title" => $post_title,
        "post_content" => $post_content,
        "post_type" => "sfwd-question",
        "post_ID" => $quiz_id,
        "action" => "new_step"
    ]);

    // STEP 3 Guardar la relacion como custom field en la pregunta de Wordpress.

    update_post_meta($cpt_question_id, "question_pro_id", $new_question_id);
    update_post_meta($cpt_question_id, "quiz_id", $quiz_id);
    update_post_meta($cpt_question_id, "question_type", "single_choice");
    update_post_meta($cpt_question_id, "question_points", 10);
    update_post_meta($cpt_question_id, "question_pro_category", 0);
    update_post_meta($cpt_question_id, "_sfwd-question", ["sfwd-question_quiz" => $new_question_id]);


    // STEP 4 - Configurar las respuestas de la pregunta.

    $url = 'http://coursefactory.local/wp-json/ldlms/v1/sfwd-questions/'.$cpt_question_id;

    
    $preguntaList = [];

    foreach($answer as $key => $value){        

        $pregunta = [
            "_answer" => $value["post_title"],
            "_correct" => $value["correct"],
            "_graded" => 1,
            "_gradedType" => "text",
            "_gradingProgression" => "",
            "_html" => false,
            "_points" => 10,
            "_sortString" => "",
            "_sortStringHtml" => false,
            "_type" => "answer"
        ];

        array_push($preguntaList, $pregunta);

    }

    



    $body = [
        "_answerData" => $preguntaList
    ];

    $args = [
    'method' => 'POST',
    'timeout' => 45,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => [
        'X-WP-Nonce' => $nonce,
        'Cookie' =>  $cookie,
        'Content-Type' => 'application/json'
    ],
    'body' => wp_json_encode( $body) 
    ];

    $response = wp_remote_post($url, $args);

    return [
        "cpt_id" => $cpt_question_id,
        "new_question_id" => $new_question_id,
        "anwsers" => $response
    ];

}