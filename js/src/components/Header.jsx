import logo from "../../public/Logo_BG_black.png";
import gear from "../../public/gear.png";
import LoadBar from "./LoadBar";

const Header = ({projectViewList, skeleton, userStatus, setUserStatus}) => {

  function settingHanlder() {
        
    if(userStatus == 1){
        setUserStatus(0) 
    }

    if(userStatus == 0){
        setUserStatus(1) 
    }

  }

  return (
    <div className="header mb-5">

      <h1>
        <img src={logo} />
        <span>{bakendi18n.learndash_integration}</span>
      </h1>

      <button className="setting" onClick={e => settingHanlder()}>
        <img src={gear} />
      </button>

      {skeleton && userStatus > 0 && (
        <>
          <LoadBar
            progresData={projectViewList}
            req_project_list={req_project_list}
          />

          <h5>{bakendi18n.load_courses}</h5>
        </>
      )}
    </div>
  );
};

export default Header;
