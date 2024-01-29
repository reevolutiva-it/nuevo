<?php

/**
 * Path: wp-content/plugins/coursefac-integration/inc/shortcode.php
 * Este archivo contiene el codigo de los shorcodes que include el plugin de wordpress.
 * Tambien hace la llamada a otros archivos que contienen codigo de shorcodes.  
 *
 */



/**
 * Esta funcion contiene el codigo del shorcode [vite_front_shorcode]
 * Este shorcode se encarga de renderizar el front de la aplicacion.
 *
 * @param [type] $atts
 * @return string
 */
function vite_front_shorcode_callback($atts){
    $atts = shortcode_atts(
        [
            'filter'        => 'employe'
        ],
        $atts
    );

    $shorcode = "<div id='my-front'></div>";

    return $shorcode;
}

add_shortcode('vite_front_shorcode','vite_front_shorcode_callback');

require "shorcodes/course_data.php";

require "shorcodes/topic_content.php";