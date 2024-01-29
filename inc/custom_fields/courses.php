<?php
/**
 * Path: wp-content/plugins/coursefac-integration/inc/custom_fields/courses.php
 * Este archivo contiene el codigo para añadir los campos personalizados a los cursos de LearnDash. */


/**
 * Esta funcion registra el campo personalizado cfact_project_version_id, cfact_project_version, cfact_project_outcomes_list para los cursos de LearnDash.
 *
 * @return void
 */
function add_cfact_project_version_id_field() {
    
    register_post_meta( 'sfwd-courses', 'cfact_project_version_id', array(
        'type' => 'string',
        'description' => 'CourseFactory Project Version ID',
        'single' => true,
        'show_in_rest' => true,
    ) );

    register_post_meta( 'sfwd-courses', 'cfact_project_version', array(
        'type' => 'string',
        'description' => 'CourseFactory Project Version',
        'single' => true,
        'show_in_rest' => true,
    ) );

    register_post_meta( 'sfwd-courses', 'cfact_project_outcomes_list', array(
        'type' => 'string',
        'description' => 'CourseFactory Project Outcomes List',
        'single' => true,
        'show_in_rest' => true,
    ) );
}

add_action( 'init', 'add_cfact_project_version_id_field' );

/**
 * Esta funcion añade el metabox para el campo personalizado cfact_project_version_id, cfact_project_version, cfact_project_outcomes_list para los cursos de LearnDash.
 *
 * @return void
 */
function cfact_project_version_id_metabox() {
    add_meta_box(
        'cfact_project_version_id_metabox',
        'CourseFactory Project',
        'cfact_project_version_id_metabox_callback',
        'sfwd-courses',
        'normal',
        'default'
    );
}

add_action( 'add_meta_boxes', 'cfact_project_version_id_metabox' );

/**
 * Esta funcion renderiza el metabox para el campo personalizado cfact_project_version_id, cfact_project_version, cfact_project_outcomes_list para los cursos de LearnDash. 
 *
 * @param [type] $post
 * @return void
 */
function cfact_project_version_id_metabox_callback( $post ) {
    $cfact_project_version_id = get_post_meta( $post->ID, 'cfact_project_version_id', true );
    $cfact_project_version = get_post_meta( $post->ID, 'cfact_project_version', true );
    $cfact_project_outcome_list = get_post_meta( $post->ID, 'cfact_project_outcome_list', true );
    ?>
    <label for="cfact_project_version_id">Project Version ID:</label>
    <input type="text" id="cfact_project_version_id" name="cfact_project_version_id" value="<?php echo esc_attr( $cfact_project_version_id ); ?>">
    <label for="cfact_project_version">Project Version:</label>
    <input type="text" id="cfact_project_version" name="cfact_project_version" value="<?php echo esc_attr( $cfact_project_version ); ?>">
    <label for="cfact_project_outcome_list">Project Outcomes List:</label>
    <textarea name="cfact_project_outcome_list" id="cfact_project_outcome_list" cols="30" rows="10"><?php echo esc_attr( $cfact_project_outcome_list ); ?></textarea>
    <?php
}

/**
 * Esta funcion tiene la logica para guardar el campo personalizado cfact_project_version_id, cfact_project_version, cfact_project_outcomes_list para los cursos de LearnDash.
 *
 * @param [type] $post_id
 * @return void
 */
function save_cfact_project_fields( $post_id ) {
    if ( isset( $_POST['cfact_project_version_id'] ) ) {
        update_post_meta( $post_id, 'cfact_project_version_id', sanitize_text_field( $_POST['cfact_project_version_id'] ) );
    }

    if ( isset( $_POST['cfact_project_version'] ) ) {
        update_post_meta( $post_id, 'cfact_project_version', sanitize_text_field( $_POST['cfact_project_version'] ) );
    }

    if ( isset( $_POST['cfact_project_outcome_list'] ) ) {
        update_post_meta( $post_id, 'cfact_project_outcome_list', sanitize_textarea_field( $_POST['cfact_project_outcome_list'] ) );
    }
}

add_action( 'save_post', 'save_cfact_project_fields' );






