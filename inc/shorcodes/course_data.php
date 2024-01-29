<?php

/**
 * Path: wp-content/plugins/coursefac-integration/inc/backend.php  */


/**
 * Esta funcion el el callback del shortcode [coursefac_course_data]. 
 * Este shorcode renderiza dentro de un curso de learndash los siguientes datos del curso que se importo desde CourseFactory.
 * outcomes_list: Lista de objetivos de aprendisaje.
 * goal_annotation: Anotacion de la meta del curso.
 * about_annotation: Anotacion sobre el curso.
 * knowledge_level: Nivel de conocimiento.
 * practical_level: Nivel practico.
 * duration: Duracion del curso.
 * 
 *
 * @param [type] $atts
 * @return string
 */
function coursefac_course_data($atts){

    // Obtengo el id del curso.
    $atts = shortcode_atts( array(
        'course-id' => '',
    ), $atts, 'coursefac_course_data' );

    // Obtengo los datos del curso.
    $cfact_project_meta = get_post_meta($atts['course-id'], 'cfact_project_meta', true);
    $cfact_project_outcome_list = get_post_meta($atts['course-id'], 'cfact_project_outcome_list', true);

    
    // Desserializo
    $cfact_project_meta = unserialize($cfact_project_meta);
    $cfact_project_outcome_list = unserialize($cfact_project_outcome_list);

    $css = ".coursefac_course_data ol{
        list-style-type: upper-latin;

    }
    .coursefac_course_data ol ol{
        list-style-type: decimal;
    ";




    $html = "<style>".$css."</style>";
    
    $html .= "<div class='coursefac_course_data'>";

    // MetaData del curso.
    $goal_annotation = $cfact_project_meta->goal_annotation;
    $about_annotation = $cfact_project_meta->about_annotation;
    $knowledge_level = $cfact_project_meta->knowledge_level;
    $practical_level = $cfact_project_meta->practical_level;
    $duration = $cfact_project_meta->duration;

   
    $html.= "<p><b>".__("Goal Annotation", "coursefactory_integration").": </b>";
    $html.= $goal_annotation."</p>";

    $html.= "<p><b>".__("About Annotaion","coursefactory_integration").": </b>";
    $html.= $about_annotation."</p>";

    $html.= "<p><b>".__("Knowledge Level" ,"coursefactory_integration").": </b>";
    $html.= $knowledge_level."</p>";

    $html.= "<p><b>".__("Practical Level", "coursefactory_integration").": </b>";
    $html.= $practical_level."</p>";

    $html.= "<p><b>".__("Duration" ,"coursefactory_integration").": </b>";
    $html.= $duration."</p>";


    // Objetivos de aprendisaje.
    $html .= "<h4>".__("Learning Outcomes" ,"coursefactory_integration")."</h4>";
    $html .= "<ol>";

    foreach($cfact_project_outcome_list as $outcome){

        $html .= "<li> <p>".$outcome->name."</p>";

        $html .= "<ol>";

        $sub_outcome_list = $outcome->sub_outcome_list;

        foreach($sub_outcome_list as $sub_outcome){
            $html .= "<li> <p>".$sub_outcome->name."</p> </li>";
        }

        $html .= "</ol>";
    }

    $html .= "</ol>";
    $html .= "</div>";

    return $html;

}

// Registro shorcode
add_shortcode('coursefac_course_data', 'coursefac_course_data');