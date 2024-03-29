<?php
/**
 * Path: wp-content/plugins/coursefac-integration/inc/course-importer.php
 * Este fichero contiene todas la funciones ivolucradas en el proceso de intepretacion del la respuesta de la API de
 * Course Factory para su importacion en LearnDash
 *
 * @package Course Factory Itegration */

/**
 * Esta funcion se encarga de interpretar la respuesta de la API de Course Factory y de crear un curso en LearnDash.
 *
 * @param mixed $course La respuesta de la API de Course Factory un Objeto PHP.
 * @param mixed $proyect_meta Un Objeto PHP con la metadata del proyecto antes de su generacion con IA.
 * @return WP_Error|int
 */
function r33v_ld_course_importer( $course, $proyect_meta ) {

	$generated_title       = $course->generated_title;
	$generated_description = $course->generated_description;
	$outcome_list          = $course->outcome_list;
	$structure_list        = $course->structure_list;
	$version               = $course->version;
	$id                    = $course->id;

	// Step 1: Create a course.

	$res = r33v_ld_course_create(
		$generated_title,
		$generated_description,
		$outcome_list,
		$structure_list,
		$version,
		$id,
		$proyect_meta
	);

	return $res;
}





/**
 * Esta funcion se encarga de crear un curso en LearnDash. Hace una insercion en
 * la tabla wp_posts con el post_type sfwd-courses y crea un custom field con el
 * id del proyecto en CourseFactory. El post-content tendra un shortcode que renderizara
 * en el front-end toda la meta-data del curso como porte del post-content del curso.
 * Dentro tambien se crean las secciones y las lecciones
 * usando sus respetivas funciones.
 *
 * @param string $title El titulo del curso.
 * @param string $description La descripcion del curso.
 * @param array  $outcome_list Un array con los objetivos del curso.
 * @param array  $structure_list Un array con la estructura del curso.
 * @param int    $version El id de la version del proyecto.
 * @param int    $id El id del proyecto.
 * @param array  $proyect_meta Un array con la metadata del proyecto antes de su generacion con IA.
 * @return int
 */
function r33v_ld_course_create( $title, $description, $outcome_list, $structure_list, $version, $id, $proyect_meta ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM `wp_postmeta` WHERE `meta_value` = %s', $id );

	$post_id = $wpdb->get_var( $sql, 1 );

	$course_atts = array(
		'post_title'   => $title,
		'post_content' => '',
		'post_status'  => 'publish',
		'author'       => 1,
		'post_type'    => 'sfwd-courses',
		'meta_input'   => array(
			'cfact_project_version'      => $version,
			'cfact_project_version_id'   => $id,
			'cfact_project_outcome_list' => serialize( $outcome_list ),
		),
	);

	if ( false === ! $post_id ) {

		wp_die( 'El curso que intentas inportar ya existe en WordPress.' );

	}

	// Creamos un curso.
	$course_id = wp_insert_post( $course_atts );

	$content = '
        <!-- wp:heading {"level":4} -->
        <h4 class="wp-block-heading">Description</h4>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph -->
        <p>' . $description . '</p>
        <!-- /wp:paragraph -->

        <!-- wp:shortcode -->
            [coursefac_course_data course-id="' . $course_id . '"]
        <!-- /wp:shortcode -->
    ';

	wp_update_post(
		array(
			'ID'           => $course_id,
			'post_content' => $content,
		)
	);

	if ( false !== $proyect_meta ) {
		$serialized_proyect_meta = serialize( $proyect_meta );
		update_post_meta( $course_id, 'cfact_project_meta', $serialized_proyect_meta );
	}

	// Step 2: Create a Secction Heading.

	r33v_ld_section_create( $course_id, $structure_list );

	// Step 3: Create a Lesson.

	foreach ( $structure_list as $key => $value ) {

		$sub_content_list = $value->sub_structure_list;

		foreach ( $sub_content_list as $item ) {

			$title              = $item->title;
			$description        = $item->description;
			$sub_structure_list = $item->sub_structure_list;

			$lession = array(
				'title'       => $title,
				'description' => $description,
			);

			r33v_ld_lesson_create( $course_id, $lession, $sub_structure_list );

		}
	}

	return $course_id;
}





/**
 * Esta funcion crea las secciones de un curso de LearnDash
 * a partir de la estructura de un curso de CourseFactory y el post_id del curso.
 *
 * @param int   $course_id post_id del curso.
 * @param mixed $structure_list array con la estructura del curso.
 * @return bool | int
 */
function r33v_ld_section_create( $course_id, $structure_list ) {

	// Recibe el ID del curso.

	$sections = array();

	$offset = 0;

	// se contruye un array con las seccioners.
	foreach ( $structure_list as $index => $section ) {

		$title              = $section->title;
		$sub_structure_list = $section->sub_structure_list;

		$counter = $index + $offset;

		$nueva_section = new CFact_LD_Section_Heading( $counter, $title );

		array_push( $sections, $nueva_section );

		$leccions_lengt = count( $sub_structure_list );

		$offset = $offset + $leccions_lengt;

	}

	// se actualiza el cpt course-section.

	$res = update_post_meta( $course_id, 'course_sections', json_encode( $sections, JSON_UNESCAPED_UNICODE ) );

	return $res;
}


/**
 * Esta funcion crea las lecciones de un curso de LearnDash a partir de la estructura de un curso de CourseFactory.
 * Dentro de esta funcion se a partir de la propiedad type en $sub_content_list se decide que tipo de contenido se va a crear
 * accionando asi una funcion espesifica para la creacion de CPT espesifico topic - quiz - otro.
 *
 * @param int   $course_id EL post_id del curso recien creado.
 * @param array $lession Un array con el titulo y la descripcion de la leccion.
 * @param array $sub_content_list Un array con la estructura de la leccion.
 * @return null
 */
function r33v_ld_lesson_create( $course_id, $lession, $sub_content_list ) {

	// Extraemos la data de lession.

	$nueva_lession = array(
		'post_title'   => $lession['title'],
		'post_content' => $lession['description'],
		'author'       => 1,
		'post_type'    => 'sfwd-lessons',
		'post_status'  => 'publish',
		'meta_input'   => array(
			'course_id' => $course_id,
		),
	);

	// Se crea la leccion.
	$lession_id = wp_insert_post( $nueva_lession );

	if ( 0 === COUNT( $sub_content_list ) ) {

		return null;
	}

	foreach ( $sub_content_list as $topic ) {

			error_log( print_r( 'topic', true ) );
			error_log( print_r( $topic, true ) );

			$content_version_id = $topic->content_version_id;
			$type               = $topic->type->name;

			$title       = $topic->title;
			$description = $topic->description;

			// Step 4: Switch = quiz, reading, video, assignment, etc.
			// Que es cada $item? Video, Lectura, Cuestionario?

		switch ( $type ) {
			case 'reading':
				r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			case 'video':
				r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			case 'quiz':
				r33v_ld_quiz_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			case 'survey':
				r33v_ld_quiz_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			case 'discussion':
				r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			case 'peer_review':
				r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );

				break;
			default:
				r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title, $description, $type );
				break;
		}
	}
}


/**
 * Esta funcion crea un topic en LearnDash y guarda su tipo en un custom field.
 * En el caso de el topic se de tipo video o reading se solicita el contenido de ese topic a la API de Course Factory y
 * se guarda como post_contetn del topic.
 *
 * @param int    $content_version_id El id de la version del contenido en Course Factory.
 * @param int    $course_id El id del curso en LearnDash.
 * @param int    $lession_id El id de la leccion en LearnDash.
 * @param string $title_old El titulo del topic.
 * @param string $description_old La descripcion del topic.
 * @param string $type_a El tipo de topic.
 * @return int
 */
function r33v_ld_topic_create( $content_version_id, $course_id, $lession_id, $title_old, $description_old, $type_a ) {

	// Obtengo el api_key de Course Factory desde wp-options.
	$api_key      = cfact_ld_api_key_mannger( 'get' );
	$contenido    = false;
	$title        = $title_old;
	$post_content = '';

	// Configuro por defecto el comment_status en closed para que el topic no admita comentarios.
	$comment_status = 'closed';

	/**
	 * Si el contenido que viene de Course Factory es de tipo "discussion" el topic si admitira comentarios.
	 * y se configurar el contenido del topic con un shortcode que renderizara el contenido de la discusion. */

	if ( 'discussion' === $type_a ) {

		$content = '<!-- wp:shortcode -->
                        [coursefac_topic_content topic="' . $description_old . '"]
                    <!-- /wp:shortcode -->
                ';

		$post_content   = $content;
		$comment_status = 'open';
	}

	/**
	 * Si el contenido que viene de Course Factory es de tipo "video" o "reading" se solicitara el contenido
	 * de ese topic a la API de Course Factory .
	 */

	if ( 'video' === $type_a || 'reading' === $type_a ) {

		// Solicitamos el contenido del topic.
		$contenido = cfac_get_content_version( $api_key, $content_version_id );
		$contenido = json_decode( $contenido );

	}

	/**
	 * Si la API de Course Factory responde con un contenido valido, se extrae el contenido y se guarda en
	 * el post_content del topic.
	 */

	if ( false !== $contenido && null !== $contenido ) :

		$data = $contenido->data;
		$type = $contenido->type;

		$title        = $data->title;
		$post_content = $data->description;

		if ( 'video' === $type ) {

			$content_list = $data->script;

			foreach ( $content_list as $content_item ) {

				if ( 'paragraph' === $content_item->content_type ) {
					$post_content .= '<p>' . $content_item->content->text . '</p>';

				}
			}
		}

		if ( 'reading' === $type ) {

			$content_list = $data->content_list;

			foreach ( $content_list as $content_item ) {

				if ( 'paragraph' === $content_item->content_type ) {
					$post_content .= '<p>' . $content_item->content->text . '</p>';

				}
			}
		}

	endif;

	// Configuramos el topic.

	$topic_data = array(
		'post_title'     => $title,
		'post_content'   => $post_content,
		'author'         => 1,
		'post_excerpt'   => $description_old,
		'post_type'      => 'sfwd-topic',
		'post_status'    => 'publish',
		'comment_status' => $comment_status,
		'meta_input'     => array(
			'course_id'        => $course_id,
			'lesson_id'        => $lession_id,
			'cfact_topic_type' => $type_a,
		),
	);

	$post_id = wp_insert_post( $topic_data );

	/**
	 *  Si el topic es de tipo "peer_review" se configura el custom_field _sfwd-topic con los
	 *  valores por defecto para subir un archivo como assigment.
	 */

	if ( 'peer_review' === $type_a ) {

		$data = array(
			'course'                             => $course_id,
			'lesson'                             => $lession_id,
			'topic_materials_enabled'            => '',
			'topic_materials'                    => '',
			'lesson_video_enabled'               => '',
			'lesson_video_url'                   => '',
			'lesson_video_shown'                 => '',
			'lesson_video_auto_start'            => '',
			'lesson_video_show_controls'         => '',
			'lesson_video_focus_pause'           => '',
			'lesson_video_track_time'            => '',
			'lesson_video_auto_complete'         => '',
			'lesson_video_auto_complete_delay'   => '',
			'lesson_video_show_complete_button'  => '',
			'lesson_assignment_upload'           => 'on',
			'assignment_upload_limit_extensions' => '',
			'assignment_upload_limit_size'       => '',
			'lesson_assignment_points_enabled'   => '',
			'lesson_assignment_points_amount'    => '',
			'assignment_upload_limit_count'      => 1,
			'lesson_assignment_deletion_enabled' => '',
			'auto_approve_assignment'            => '',
			'forced_lesson_time_enabled'         => '',
			'forced_lesson_time'                 => '',
			'lesson_video_hide_complete_button'  => '',
			'lesson_schedule'                    => '',
			'visible_after'                      => '',
			'visible_after_specific_date'        => '',
		);

		foreach ( $data as $key => $value ) {
			learndash_update_setting( $post_id, $key, $value );
		}
	}

	return $post_id;
}

/**
 * Esta funcion crea un quiz en LearnDash, esto implica la afetacion de 3 tablas: wp-post para guardar el cpt
 * quizPro-master para guardar la metadata del quiz y post-meta para guardar la relacion de quizPro-master con el cpt.
 * Dentro de esta funcion se solicita el contenido del quiz a la API de Course Factory y se crea el cpt-question con su respectiva
 * funcion.
 *
 * @param int    $content_version El id de la version del contenido en Course Factory.
 * @param int    $course_id El id del curso en LearnDash.
 * @param int    $lession_id El id de la leccion en LearnDash.
 * @param string $title El titulo del quiz.
 * @param string $description La descripcion del quiz.
 * @param string $type_a El tipo de quiz.
 * @return null | string
 */
function r33v_ld_quiz_create( $content_version, $course_id, $lession_id, $title, $description, $type_a ) {

	if ( ! empty( $content_version ) ) {

		// Obtiene la api_key de Course Factory desde wp-options.
		$api_key = cfact_ld_api_key_mannger( 'get' );

		// Guardamos la relacion entre el quiz y el curso.
		$meta_input = array(
			'course_id' => $course_id,
			'lesson_id' => $lession_id,
		);

		// Configuramos el quiz.
		$quiz_data = array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_type'    => 'sfwd-quiz',
			'post_status'  => 'publish',
			'meta_input'   => $meta_input,
		);

		// Creamos el cpt-quiz.
		$post_id = wp_insert_post( $quiz_data );

		$quiz_id = 0;

		$quiz_mapper = new WpProQuiz_Model_QuizMapper();
		$quiz        = new WpProQuiz_Model_Quiz();

		// Creamos quiz en la tabla quiz_pro.

		$quiz->setId( $quiz_id );
		$quiz->setName( $quiz_data['post_title'] );
		$quiz->setText( $quiz_data['post_content'] );
		$quiz->setPostId( $post_id );
		$quiz_mapper->save( $quiz );

		// Guardamos el id como un cpt_meta.

		update_post_meta( $post_id, 'quiz_pro', $quiz->getId() );
		update_post_meta( $post_id, 'quiz_pro_primary_' . $quiz->getId(), $quiz->getId() );

		$quiz_settings = array(
			'quiz_pro'                            => $quiz->getId(),
			'course'                              => $course_id,
			'lesson'                              => $lession_id,
			'lesson_schedule'                     => '',
			'visible_after'                       => '',
			'visible_after_specific_date'         => '',
			'startOnlyRegisteredUser'             => false,
			'prerequisiteList'                    => '',
			'prerequisite'                        => '',
			'retry_restrictions'                  => '',
			'quiz_resume'                         => false,
			'quiz_resume_cookie_send_timer'       => 20,
			'repeats'                             => '',
			'quizRunOnceType'                     => '',
			'quizRunOnceCookie'                   => '',
			'passingpercentage'                   => '80',
			'certificate'                         => '',
			'threshold'                           => '',
			'quiz_time_limit_enabled'             => '',
			'timeLimit'                           => 0,
			'forcingQuestionSolve'                => false,
			'quizRunOnce'                         => false,
			'quiz_materials_enabled'              => '',
			'quiz_materials'                      => '',
			'custom_sorting'                      => '',
			'autostart'                           => false,
			'showReviewQuestion'                  => false,
			'quizSummaryHide'                     => true,
			'skipQuestionDisabled'                => true,
			'sortCategories'                      => false,
			'questionRandom'                      => '',
			'showMaxQuestion'                     => '',
			'showMaxQuestionValue'                => '',
			'showPoints'                          => false,
			'showCategory'                        => false,
			'hideQuestionPositionOverview'        => true,
			'hideQuestionNumbering'               => true,
			'numberedAnswer'                      => false,
			'answerRandom'                        => false,
			'quizModus'                           => 0,
			'quizModus_multiple_questionsPerPage' => 0,
			'quizModus_single_back_button'        => '',
			'quizModus_single_feedback'           => 'end',
			'titleHidden'                         => true,
			'custom_question_elements'            => '',
			'resultGradeEnabled'                  => false,
			'resultText'                          => '',
			'btnRestartQuizHidden'                => false,
			'showAverageResult'                   => '',
			'showCategoryScore'                   => '',
			'hideResultPoints'                    => false,
			'hideResultCorrectQuestion'           => false,
			'hideResultQuizTime'                  => false,
			'hideAnswerMessageBox'                => false,
			'disabledAnswerMark'                  => false,
			'btnViewQuestionHidden'               => false,
			'custom_answer_feedback'              => 'on',
			'custom_result_data_display'          => 'on',
			'associated_settings_enabled'         => '',
			'toplistDataShowIn_enabled'           => '',
			'statisticsIpLock_enabled'            => '',
			'formActivated'                       => false,
			'formShowPosition'                    => '0',
			'toplistDataAddPermissions'           => '1',
			'toplistDataAddMultiple'              => false,
			'toplistDataAddBlock'                 => 0,
			'toplistDataAddAutomatic'             => false,
			'toplistDataShowLimit'                => 0,
			'toplistDataSort'                     => '1',
			'toplistActivated'                    => false,
			'toplistDataShowIn'                   => 0,
			'toplistDataCaptcha'                  => false,
			'statisticsOn'                        => true,
			'viewProfileStatistics'               => true,
			'statisticsIpLock'                    => 0,
			'email_enabled'                       => '',
			'email_enabled_admin'                 => '',
			'emailNotification'                   => 0,
			'userEmailNotification'               => false,
			'timeLimitCookie_enabled'             => '',
			'timeLimitCookie'                     => '',
			'templates_enabled'                   => '',
			'custom_fields_forms'                 => '',
			'advanced_settings'                   => '',
		);

		foreach ( $quiz_settings as $key => $value ) {
			learndash_update_setting( $post_id, $key, $value );
		}

		// Si el quiz es de tipo "survey" no se solicita el contenido del quiz a la API de Course Factory.
		if ( 'survey' === $type_a ) {
			return '';
		}

		// Solicitamos el contenido del quiz.

		$contenido = cfac_get_content_version( $api_key, $content_version );
		$contenido = json_decode( $contenido );

		if ( $contenido->data ) {

			$data = $contenido->data;

			$title       = $data->title;
			$description = $data->description;

			$content_list = $data->content_list;

			// $content_list es un array de preguntas.

			foreach ( $content_list as $question ) {

				$answers  = $question->content_list;
				$question = $question->content;

				$respuestas = array();

				foreach ( $answers as $answer ) {

					$content   = $answer->content;
					$is_answer = $answer->is_answer;

					$respuesta = array(
						'post_title' => $content,
						'correct'    => $is_answer,
					);

					array_push( $respuestas, $respuesta );

				}
			}

			$nueva_pregunta = array(
				'post_title'   => $question,
				'post_content' => $question,
				'quiz_id'      => $post_id,
				'answer'       => $respuestas,
			);

			// Creamos las preguntas del quiz.
			r33v_ld_question_create( $nueva_pregunta );
		}
	}
}

/**
 * Esta funcion crea una pregunta en LearnDash, esto implica la afetacion de 3 tablas: wp-post para guardar el cpt
 * questionPro-question para guardar la metadata de la pregunta y post-meta para guardar la relacion de questionPro-question con el cpt.
 * dentro de esta funcion se llama a otra funcion para crear las respuestas de la pregunta.
 *
 * @param array $nueva_pregunta Un array con el titulo y la descripcion de la pregunta.
 * @return void
 */
function r33v_ld_question_create( $nueva_pregunta ) {

	// Creamos el CPT Pregunta.

	$cpt_question_id = wp_insert_post(
		array(
			'post_title'   => $nueva_pregunta['post_title'],
			'post_content' => $nueva_pregunta['post_content'],
			'post_type'    => 'sfwd-question',
			'post_status'  => 'publish',
		)
	);

	// Creamos la pregunta en Learndash.

	$new_question_id = learndash_update_pro_question(
		0,
		array(
			'post_title'   => $nueva_pregunta['post_title'],
			'post_content' => $nueva_pregunta['post_content'],
			'post_type'    => 'sfwd-question',
			'post_ID'      => $nueva_pregunta['quiz_id'],
			'action'       => 'new_step',
		)
	);

	// Guardamos la relacion como custom field en la pregunta de WordPress.

	update_post_meta( $cpt_question_id, 'question_pro_id', $new_question_id );
	update_post_meta( $cpt_question_id, 'quiz_id', $nueva_pregunta['quiz_id'] );
	update_post_meta( $cpt_question_id, 'question_type', 'single_choice' );
	update_post_meta( $cpt_question_id, 'question_points', 10 );
	update_post_meta( $cpt_question_id, 'question_pro_category', 0 );
	update_post_meta( $cpt_question_id, '_sfwd-question', array( 'sfwd-question_quiz' => $new_question_id ) );

	// Configurar las respuestas de la pregunta.

	$pregunta_list = array();

	foreach ( $nueva_pregunta['answer'] as $key => $value ) {

		$pregunta = array(
			'_answer'             => $value['post_title'],
			'_correct'            => $value['correct'],
			'_graded'             => 1,
			'_gradedType'         => 'text',
			'_gradingProgression' => '',
			'_html'               => false,
			'_points'             => 10,
			'_sortString'         => '',
			'_sortStringHtml'     => false,
			'_type'               => 'answer',
		);

		array_push( $pregunta_list, $pregunta );

	}

	r33v_ld_answer_create( $pregunta_list, $cpt_question_id );
}

/**
 * Esta funcion crea las respuesta de una pregunta en LearnDash.
 * Acutalizando la propiedad _answerData de la pregunta en la tabla quizPro-question.
 *
 * @param array $pregunta_list Un array con las respuestas de la pregunta.
 * @param int   $question_id El id de la pregunta en LearnDash en la tabal quizPro-question.
 * @return void
 */
function r33v_ld_answer_create( $pregunta_list, $question_id ) {

	// Guardamos las respuestas como el valor de una propiedad llamada _answerData.
	$question_data = array(
		'_answerData' => $pregunta_list,
	);

	// Instanciamos el mapper de preguntas.
	$question_pro_id = (int) get_post_meta( $question_id, 'question_pro_id', true );

	$question_mapper = new \WpProQuiz_Model_QuestionMapper();
	$question_model  = $question_mapper->fetch( $question_pro_id );

	// Update the question object with new data.
	$question_model->set_array_to_object( $question_data );

	// Save the new data to database.
	$question_mapper->save( $question_model );
}
