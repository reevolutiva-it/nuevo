// Importo componentes
import CF_Proyect from "./components/CF_Proyect";
import CF_Proyect_New from "./components/CF_Proyect_New";
import CF_Proyect_Skeleton from "./components/CF_Proyect_Skeleton";

const ListingProyect = ({projectViewList, req_project_list, skeleton, post_id, setPost_id}) => {
    return ( 
        <>

        <div className="cfact-proyecto">

        {
          !skeleton &&
          projectViewList.map((item, index) => {
            return  <CF_Proyect
                      index={index}
                      key={index}
                      proyecto={item}
                      title={item.generated_title}
                      id={item.id}
                      post_id={post_id}
                      setPost_id={setPost_id}
                    />
          })
        }

        {
          skeleton &&
          req_project_list.map((item, index) => {
            return  <CF_Proyect_Skeleton
                      index={index}
                      key={index}
                      proyecto={item}
                    />
          })
        }
        <CF_Proyect_New />
      </div>
      
      </>
     );
}
 
export default ListingProyect;