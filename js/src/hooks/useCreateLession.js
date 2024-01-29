const useCreateLession = (props, callback) => {
    /*
        {
  "date": "2023-02-15T10:30:00",
  "date_gmt": "2023-02-15T14:30:00",
  "slug": "mi-primera-leccion",
  "status": "publish",
  "password": "",
  "title": "Mi primera lección",
  "content": "Contenido de mi primera lección",
  "author": 12345,
  "featured_media": 98765,
  "comment_status": "open",
  "ping_status": "open",
  "menu_order": 1,
  "meta": {
    "duration": "2 hours" 
  },
  "template": "single-lesson.php",
  "categories": [1, 5, 8],
  "tags": [10, 15],
  "ld_lesson_category": [3],
  "ld_lesson_tag": [5, 9],
  "materials_enabled": true,
  "materials": ["https://ejemplo.com/guia.pdf"],
  "video_enabled": true, 
  "video_url": "https://www.youtube.com/embed/abcd1234",
  "video_shown": "AFTER",
  "video_auto_complete": true,
  "video_auto_complete_delay": 10,
  "video_show_complete_button": true,
  "video_auto_start": false,
  "video_show_controls": true,
  "video_focus_pause": true,
  "video_resume": true,
  "assignment_upload_enabled": true,
  "assignment_upload_limit_extensions": "pdf,doc",
  "assignment_upload_limit_size": 10000000,
  "assignment_points_enabled": true,
  "assignment_points_amount": 15,
  "assignment_auto_approve": false,
  "assignment_upload_limit_count": 1,
  "assignment_deletion_enabled": true,
  "forced_timer_enabled": true,
  "forced_timer_amount": 3600,
  "course": 567,
  "is_sample": false,
  "visible_type": "visible_after_specific_date",
  "visible_after": 0,
  "visible_after_specific_date": "2023-03-01"
}
    */

  

    // Hago una peticion POST a el endpoint /ldlms/v2/sfwd-lessons usando el metodo wp.apiRequest.
    wp.apiRequest({
        method: 'POST',
        path: '/ldlms/v2/sfwd-lessons',
        data:props
    }).then( response => {
        // Si no hay errores, ejecuto el callback.
        if(!false){
            callback(response)
        }
        
    }).catch( error => {
        // Si hay errores, los muestro en consola.
        console.log(error);
    });
}

// Creo un custom hook para actualizar lecciones.
const useUpdateLession = (props, id , callback) =>{
    // Hago un peticion a la url https://reevolutiva.com/wp-json/ldlms/v2/sfwd-lessons usando wpApiRequest
    wp.apiRequest({
        path: '/ldlms/v2/sfwd-lessons/'+id,
        method: 'POST',
        data: props,
        // ... otros parametros
    }).then( response => {

        if(!false){
            callback(response)
        }
        
    }).catch( error => {
        console.log(error);
    });    
}

// Custom hook para actualizar lecciones
const useAfterCreateUpdateLesson = (courseId, lesson_id, callback) => {

    // Petición al endpoint PHP 
    wp.apiRequest({
      path: '/reev-cf-ld-integration/v1/lesson', 
      method: 'POST',
      data: {
        course_id: courseId,
        lesson_id: lesson_id
      }
    }).then(response => {
      if(!response.error){
        callback(response);
      }
    }).catch(error => {
      console.log(error); 
    });
  
  }

export { useCreateLession,useUpdateLession, useAfterCreateUpdateLesson };
