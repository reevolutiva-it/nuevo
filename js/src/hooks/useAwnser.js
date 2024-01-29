const useCreateAwnser = ({post_title, post_content, quiz_id, nonce, cookie, answer}, callback=false) => {
    wp.apiRequest({
        method:'POST',
        path:'/cfact/v1/cfact-questions',
        data: JSON.stringify({
            "post_title" : post_title,
            "post_content" : post_content,
            "quiz_id": quiz_id,
            "nonce":  nonce,
            "cookie": cookie,
            "answer": answer
        })
        
    }).then(e => {
        if(callback != false){
            callback(e);
        }
    }).catch(error => {
        error.log(error);
    });
}



export { useCreateAwnser };