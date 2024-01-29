import{ useEffect, useState } from 'react';

/**
 * Este hook crea un curso en LearnDash usando la api de WordPress.
 *
 * @param {*} requestData es un objeto con los datos del curso.
 * @param {boolean} [callback=false]
 */
const useCreateCourse = (requestData, callback = false) => {

 // Hago un peticion a la url https://reevolutiva.com/wp-json/ldlms/v2/sfwd-courses usando wpApiRequest
    wp.apiRequest({
        path: '/ldlms/v2/sfwd-courses',
        method: 'POST',
        data: requestData,
        // ... otros parametros
    }).then( response => {

        if(!false){
            callback(response)
        }
        
    }).catch( error => {
        console.log(error);
    });    

};

// Esta funcion actualiza en LearnDash usando la api de WordPress.
const useUpdateCourse = (requestData, id , callback) =>{
    // Hago un peticion a la url https://reevolutiva.com/wp-json/ldlms/v2/sfwd-courses usando wpApiRequest
    wp.apiRequest({
        path: '/ldlms/v2/sfwd-courses/'+id,
        method: 'POST',
        data: requestData,
        // ... otros parametros
    }).then( response => {

        if(!false){
            callback(response)
        }
        
    }).catch( error => {
        console.log(error);
    });    
}

// Esta funcion elimina un curso en LearnDash usando la api de WordPress.
const useDeleteCoruse  = (requestData, id , callback) =>{
    // Hago un peticion a la url https://reevolutiva.com/wp-json/ldlms/v2/sfwd-courses usando wpApiRequest
    wp.apiRequest({
        path: '/ldlms/v2/sfwd-courses/'+id,
        method: 'DELETE',
        data: requestData,
        // ... otros parametros
    }).then( response => {

        if(!false){
            callback(response)
        }
        
    }).catch( error => {
        console.log(error);
    }); 
}

export { useCreateCourse, useUpdateCourse, useDeleteCoruse };