<?php
/**
 * Path: wp-content/plugins/coursefac-integration/i18n/backend_i18n.php
 * Este archivo contiene un array con las traducciones de los textos del backend del plugin CourseFactory Integration.*/


/**
 * Esta funcion retorna un array con las traducciones de los textos del que utlizara la apliacion react que carga en la pagina de administracion del plugin CourseFactory Integration.
 *
 * @return array 
 */
function cousefact_backend_i18n(){

    $backend_i18n = [
        'insert_your_api_key' => __('Insert Your Api Key:', 'coursefactory_integration'),  
        'delete_api_key' => __('Delete Api Key:', 'coursefactory_integration'),  
        'where_to_fin_api:' => __('Where to find my API Key?', 'coursefactory_integration'),  
        'conect_to_course_factory' => __('Connect to CourseFactory AI Copilot', 'coursefactory_integration'),  
        'you_dont_have' => __('You don\'t have an Account?', 'coursefactory_integration'),  
        'open_you_free' => __('Open Your free Account now', 'coursefactory_integration'),  
        'view_course' => __('View course', 'coursefactory_integration'),
        'import_course' => __('Import course', 'coursefactory_integration'),
        'course_factory_integration' => __('Course Factory Integration', 'coursefactory_integration'),
        'creating_course' => __('Creating course', 'coursefactory_integration'),
        'could_lated_a_moment' => __('Could take a moment', 'coursefactory_integration'),
        'loading' => __('Loading', 'coursefactory_integration'),
        'version' => __('Version', 'coursefactory_integration'),
        'reimport_course' => __('Reimport course', 'coursefactory_integration'),
        'delete_from_leardash' => __('Delete from LearDash', 'coursefactory_integration'),
        'delelte_course_confirm' => __('This will delete the course and all associated elements in Leandash. Do you want to continue?', 'coursefactory_integration'),
        'go_to_leardash' => __('Go to LearDash', 'coursefactory_integration'),
        'continue' => __('Continue', 'coursefactory_integration'),
        'reimport_course_confirm' => __('This will delete your old version of the course in learndash and create a new one. do you wish to continue?', 'coursefactory_integration'),
        'course_deleted' => __('Course deleted success', 'coursefactory_integration'),
        'load_courses' => __('Loading courses from CourseFactory Copilot', 'coursefactory_integration'),
        'error_course_import_reload' => __('Oops, an error occurred, the course cannot be deleted, please reload the page and try again. Do you want to reload the page?', 'coursefactory_integration'),
        'learndash_integration' => __('Learndash integration', 'coursefactory_integration'),
        'new_project' => __('New Project', 'coursefactory_integration'),
    ];

    return $backend_i18n;
}

