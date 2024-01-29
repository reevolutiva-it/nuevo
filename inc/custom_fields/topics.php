<?php
/**
 * Path: wp-content/plugins/coursefac-integration/inc/custom_fields/topics.php
 * Este archivo contiene el codigo para añadir los campos personalizados a los temas de LearnDash. */

/**
 * Esta funcion registra el campo personalizado cfact_topic_type para los temas de LearnDash.
 *
 * @return void
 */
function register_cfact_topic_type_field() {
    register_post_meta('sfwd-topic', 'cfact_topic_type', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
}

/**
 * Esta funcion añade el metabox para el campo personalizado cfact_topic_type para los temas de LearnDash.
 *
 * @return void
 */
function add_cfact_topic_type_metabox() {
    add_meta_box(
        'cfact_topic_type_metabox',
        'CFact Topic Type',
        'render_cfact_topic_type_metabox',
        'sfwd-topic',
        'normal',
        'default'
    );
}

/**
 * Esta funcion renderiza el metabox para el campo personalizado cfact_topic_type para los temas de LearnDash.
 *
 * @param [type] $post
 * @return void
 */
function render_cfact_topic_type_metabox($post) {
    
    $value = get_post_meta($post->ID, 'cfact_topic_type', true);
    ?>
    <label for="cfact_topic_type">CFact Topic Type:</label>
    <select id="cfact_topic_type" name="cfact_topic_type">
        <option value="quiz" <?php selected($value, 'quiz'); ?>><?php echo __("Quiz", "coursefactory_integration")?></option>
        <option value="video" <?php selected($value, 'video'); ?>><?php echo __("Video", "coursefactory_integration")?></option>
        <option value="reading" <?php selected($value, 'reading'); ?>><?php echo __("Reading", "coursefactory_integration")?></option>
        <option value="peer_review" <?php selected($value, 'peer_review'); ?>><?php echo __("Peer Review", "coursefactory_integration")?></option>
        <option value="discussion" <?php selected($value, 'discussion'); ?>><?php echo __("Discussion", "coursefactory_integration")?></option>
        <option value="group_work" <?php selected($value, 'group_work'); ?>><?php echo __("Group Work", "coursefactory_integration")?></option>
        <option value="lecture" <?php selected($value, 'lecture'); ?>><?php echo __("Lecture", "coursefactory_integration")?></option>
        <option value="chat" <?php selected($value, 'chat'); ?>><?php echo __("Chat", "coursefactory_integration")?></option>
        <option value="survey" <?php selected($value, 'survey'); ?>><?php echo __("Survey", "coursefactory_integration")?></option>
        <option value="assignment" <?php selected($value, 'assignment'); ?>><?php echo __("Assignment", "coursefactory_integration")?></option>
    </select>
    <?php
}

/**
 * Esta funcion procesa el campo personalizado cfact_topic_type para los temas de LearnDash.
 *
 * @param int $post_id
 * @return void
 */
function save_cfact_topic_type_metabox($post_id) {
    if (isset($_POST['cfact_topic_type'])) {
        update_post_meta($post_id, 'cfact_topic_type', sanitize_text_field($_POST['cfact_topic_type']));
    }
}

add_action('init', 'register_cfact_topic_type_field');
add_action('add_meta_boxes', 'add_cfact_topic_type_metabox');
add_action('save_post_sfwd-topic', 'save_cfact_topic_type_metabox');



