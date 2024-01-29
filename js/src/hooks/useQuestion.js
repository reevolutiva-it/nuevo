/*
{
  "date": "2023-02-15T12:00:00",
  "date_gmt": "2023-02-15T17:00:00", 
  "guid": {
    "rendered": "https://example.com/?post_type=post&p=123"  
  },
  "id": 123,
  "link": "https://example.com/my-post/",
  "modified": "2023-02-16T09:30:00",
  "modified_gmt": "2023-02-16T14:30:00",
  "slug": "my-post",
  "status": "publish",
  "type": "post",
  "password": "",
  "permalink_template": "/%year%/%monthnum%/%day%/%postname%/",
  "generated_slug": "",
  "title": {
    "raw": "My Example Post",
    "rendered": "<h1>My Example Post</h1>"
  },
  "content": {
    "raw": "", 
    "rendered": ""
  },
  "author": 35,
  "featured_media": null,
  "comment_status": "open",
  "ping_status": "open",
  "menu_order": 0,
  "template": "",
  "categories": [1, 2], 
  "tags": [5, 8],
  "ld_Question_category": [],
  "ld_Question_tag": [],
  "course": null,
  "lesson": null,
  "prerequisites": [],
  "registered_users_only": false,
  "passing_percentage": 80,
  "certificate_award_threshold": 100,
  "retry_restrictions_enabled": false, 
  "retry_repeats": "0",
  "answer_all_questions_enabled": true,
  "time_limit_enabled": false,
  "time_limit_time": 60,
  "materials_enabled": false,
  "materials": "",
  "auto_start": false,
  "Question_modus": "single",
  "review_table_enabled": true,
  "summary_hide": false,
  "skip_question_disabled": false,
  "custom_sorting": false,
  "sort_categories": false,
  "question_random": false, 
  "show_max_question": false,
  "show_points": true,
  "show_category": false,
  "hide_question_position_overview": false,
  "hide_question_numbering": false,
  "numbered_answer": false,
  "answer_random": false,
  "title_hidden": false,
  "restart_button_hide": false,
  "show_average_result": true,
  "show_category_score": false,
  "hide_result_points": false,
  "hide_result_correct_question": false,
  "hide_result_Question_time": false,
  "custom_answer_feedback": false,
  "hide_answer_message_box": false,
  "disabled_answer_mark": false,
  "view_question_button_hidden": false,
  "toplist_enabled": false,
  "toplist_data_add_permissions": "1",
  "toplist_data_add_multiple": false,
  "toplist_data_add_automatic": false,
  "toplist_data_show_limit": 10,
  "toplist_data_sort": "1",
  "toplist_data_showin_enabled": false,
  "statistics_enabled": false,
  "view_profile_statistics_enabled": false,
  "statistics_ip_lock_enabled": false,
  "email_enabled": false,
  "email_admin_enabled": false,
  "email_user_enabled": false,
  "certificate": null
}
*/

const useGetListQuestion = (id, callback=false) => {
    wp.apiRequest({
        method:'GET',
        path:'/ldlms/v2/sfwd-question/'+id,
        
    }).then(e => {
        if(callback != false){
            callback(e);
        }
    }).catch(error => {
        error.log(error);
    });
}

const useCreateQuestion = (props, callback = false) => {
    wp.apiRequest({
        method:'POST',
        path:'/ldlms/v2/sfwd-question',
        data:props
    }).then(e => {
        if(callback != false){
            callback(e);
        }
    }).catch(error => {
        error.log(error);
    });
};

const useUpdateQuestion = (props, id, callback = false) => {
    wp.apiRequest({
        method:'POST',
        path:'/ldlms/v2/sfwd-question'+id,
        data:props
    }).then(e => {
        if(callback != false){
            callback(e);
        }
    }).catch(error => {
        error.log(error);
    });
};


export { useCreateQuestion , useUpdateQuestion, useGetListQuestion };