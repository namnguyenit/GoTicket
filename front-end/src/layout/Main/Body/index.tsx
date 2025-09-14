import style from "./Body.module.css";
import Home from "../../../page/Home";

function Body() {
  return (
    <>
      <div className={style.box}>
        <Home />
      </div>
    </>
  );
}

export default Body;
