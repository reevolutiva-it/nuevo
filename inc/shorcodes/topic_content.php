<?php

// Path: wp-content/plugins/coursefac-integration/inc/backend.php

/**
 * Esta funcion es el callback de el shorcode [coursefac_topic_content].
 * Este shorcode renderiza dentro de un topic de learndash un div con el id topic_comment_box
 * que se usara para renderizar los comentarios de los usuarios.
 *
 * @param [type] $atts
 * @return string
 */
function coursefac_topic_content($atts){

    // Obtengo el id del curso.
    $atts = shortcode_atts( array(
        'topic' => '',
    ), $atts, 'coursefac_topic_content' );





    $html = "<div>";
    $html .= "<h3>".$atts["topic"]."</h3>";
    $html .= "<div id='topic_comment_box'></div>";
    $html .= "</div>";

    return $html;

}

// Registro shorcode
add_shortcode('coursefac_topic_content', 'coursefac_topic_content');

add_action("wp_footer", "coursefac_topic_content_js");

function coursefac_topic_content_js(){
    ?>

    <script>

        if(location.href.includes("topics")){
            const topic_comment_box = document.querySelector('#topic_comment_box');
            if(topic_comment_box){
                topic_comment_box.append(document.querySelector('.wp-block-comments'));
            }
            
        }    

    </script>

    <?php
}