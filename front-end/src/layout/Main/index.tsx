import style from "./Main.module.css";
import Navigator from "../../components/Navigator";
import Body from "./Body";
import Footer from "./Footer";

function Main() {
  return (
    <>
      <div className={style.box}>
        <div className={style.nav}>
          <Navigator />
        </div>
        <Body />
        <Footer />
      </div>
    </>
  );
}

export default Main;
