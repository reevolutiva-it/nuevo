import { useEffect, useState } from "react";
import "../styles/LoginForm.scss";

const LoginForm = () => {

    const [api_key, setApi_key] = useState(cfact_learndash_integration_apiKey);


    function hanndleInput(e){

        const contenido = e.target.value;
        setApi_key(contenido);
        
    }

    return ( 
        <div className="LoginForm">          
            <section>
                <h3>{bakendi18n.insert_your_api_key}</h3>
                <a target="_blank" href="https://cob.coursefactory.net/login">
                    <p>{bakendi18n.where_to_fin_api}</p>
                </a>
            </section> 
            <form method="GET" action={`${location.origin}/wp-admin/admin.php?`}>
                    <input type="hidden" name="page" value="course_factory_integration"  />
                    <input type="hidden" name="set-api_key"  />
                    <label>
                          {
                                cfact_learndash_integration_apiKey !== "" ? 
                                <input disabled="true" type="text" value={api_key}  onChange={e => hanndleInput(e)} name="api-key" placeholder="*********" /> : 
                                <input type="text" value={api_key}  onChange={e => hanndleInput(e)} name="api-key" placeholder="*********" />
                          }
                          
                          {cfact_learndash_integration_apiKey !== "" && <p><a href={`${location.origin}/wp-admin/admin.php?page=course_factory_integration&delete-api_key`}>{bakendi18n.delete_api_key}</a></p> }
                    </label>
                    <button type="submit" className="btn-cfac mt-3">{bakendi18n.conect_to_course_factory}</button>
            </form>

            <section className="mt-3 LoginForm--footer">
                <p>{bakendi18n.you_dont_have}  <a target="_blank" href="https://cob.coursefactory.net/signup?promo=940893664"> <b>{bakendi18n.open_you_free}</b> </a> </p>
            </section>
            

            
        </div>
     );
}
 
export default LoginForm;