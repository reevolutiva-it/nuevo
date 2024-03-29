import React from 'react';
import { useEffect, useState } from 'react';

const CF_Proyect = ({ title, proyecto, id, post_id, setPost_id }) => {

    const [imported, setImported] = useState(false);
    const [postID, setPostID] = useState(false);
  
    function getClass(imported){
        
        if(imported){
          return 'cfact-proyecto-item col-4 imported';
        }
    
        return 'cfact-proyecto-item col-4';
    }
  
    useEffect( () => {
      if(proyecto.post_id){
        setPostID(proyecto.post_id);
      }
    }, [proyecto]);
  
    useEffect( () =>{
      if(proyecto.exist == "true"){
        setImported(true);
      }
    },[proyecto.exist]);
  
    function importProyect(id, imported, post_id) {
  
      let reimport = false;
  
  
  
      if(imported){
  
          const preg = confirm(bakendi18n.reimport_course_confirm);
  
          if(!preg){
            return true;
          }
  
          reimport = post_id;
      }
  
  
      document.querySelector('.load-modal').setAttribute("open", "true");
      
      
      wp.apiRequest({
        path: `cfact/v1/cfact-projects-import?project_import_id=${id}&reimport=${reimport}`,
        method: "POST"
      }).then(
        (response) => {
          console.log(response);
          const {status, course_id} = response;
          if(status == "ok"){
            setPost_id(course_id);
            setPostID(course_id);
            setImported(true);
  
          }
        }
      ).catch((error) => {
        console.error(error);
      });
      
      
    }
  
    function handleDelete(e, post_id){
  
      e.preventDefault();
  
      if(post_id == undefined || post_id == 'undefined'){
  
        const query = confirm(bakendi18n.error_course_import_reload);
  
        if(query){
          // Recarga la pestaña acutal
          location.reload();
        }
  
        return false;
      }
      
      const preg = confirm(bakendi18n.delelte_course_confirm);
  
      if(preg){    
  
        wp.apiRequest({
          path: "/cfact/v1/cfact-curso-delete",
          method: "DELETE",
          data: JSON.stringify({
            "course_id": post_id,
        }),
        }).then((response) => {
  
            if(response == 200){
              const nodo = e.target;
              setImported(false);
  
              alert(bakendi18n.course_deleted);
            }
  
          console.log(response);
        }).catch((error) => {
          console.error(error);
        });
  
      }
      
    }
  
    return (
      <div className={getClass(imported)}>
  
        <span className="imported-tag">
          <a target="_blank" href={`${location.origin}?p=${postID}`}>{ bakendi18n.go_to_leardash }</a>
        </span>
  
        <div className="cfact-proyecto-item__inside">
          <h3>{title}</h3>
  
          <p className="version-item">{bakendi18n.version} : {proyecto.version}</p>
  
          <div className="cfact-proyecto-item__footer">
                      
              <button className="btn btn-delete" onClick={e => handleDelete(e, postID)}>
                { bakendi18n.delete_from_leardash }
              </button>
              <button className="btn btn-cfact" onClick={(e) => importProyect(id, imported, postID)}>
                { !imported && bakendi18n.import_course }
                { imported && bakendi18n.reimport_course}
              </button>
              
          </div>
        </div>
      </div>
    );
  };

  export default CF_Proyect;