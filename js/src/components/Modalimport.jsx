import React from 'react';
import { useEffect, useState } from 'react';

function ModalImport ({post_id}){

    const [load, setLoad] = useState(false);
  
    useEffect( () => {
        
        if(post_id){
          setLoad(true);
        }
    }, [post_id]);
    
  
    function hanndleRedirect(e){
  
      e.preventDefault();
  
      const url = `${location.origin}?p=${post_id}`;
      window.open(url, '_blank');
      document.querySelector('.load-modal').removeAttribute("open");
  
    }
  
    function handleSubmit(e){
      setLoad(false);
      document.querySelector('.load-modal').removeAttribute("open");
    }
  
    return <dialog className="load-modal">
  
              <div className="overlay"></div>
              <div className="inner">
                <h1>{bakendi18n.creating_course}</h1>
                <p>{bakendi18n.could_lated_a_moment}</p>
    
              {
                load ?
              
              <div className="d-flex justify-content-between mt-3">
                <button className="btn btn-cfact" onClick={e => hanndleRedirect(e)} >{bakendi18n.view_course}</button>
                <form method="dialog" onSubmit={e => handleSubmit(e)}>
                  <button className="btn btn-cfact gosth" >{bakendi18n.continue}</button>
                </form>
              </div> 
              :
              <>
                <div
                className="spinner-grow cfact-color cfact-color-dark"
                role="status"
              >
                <span className="sr-only"></span>
              </div>
              <div className="spinner-grow cfact-color" role="status">
                <span className="sr-only"></span>
              </div>
              <div
                className="spinner-grow cfact-color cfact-color-dark"
                role="status"
              >
                <span className="sr-only"></span>
              </div>
              </> 
              }          
              </div>
            </dialog>
  }

  export default ModalImport;