const useCreateTopic = (props, callback) => {

    // Hago una peticion POST a el endpoint /ldlms/v2/sfwd-lessons usando el metodo wp.apiRequest.
    wp.apiRequest({
        method: 'POST',
        path: '/ldlms/v2/sfwd-topic',
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


export { useCreateTopic};
