import { Link } from "react-router-dom";

import style from "./Navigator.module.css";
import clsx from "clsx";
import logo from "/src/assets/logo.png";

function Navigator() {
  return (
    <>
      <div className={style.box}>
        <img className={style.logo} src={logo}></img>
        <div className={style.wrap}>
          <div className={style.navBar}>
            <Link to="/">
              <div className={style.link}>Home</div>
            </Link>
            <Link to="/book">
              <div className={style.link}>Book</div>
            </Link>
            <div className={style.link}>About</div>
            <div className={style.link}>Blog</div>
            <div className={style.link}>Contact</div>
          </div>
          <div className={style.auth}>
            <div className={clsx(style.register, style.button)}>Register</div>
            <div className={clsx(style.logIn, style.button)}>Sign in</div>
          </div>
        </div>
      </div>
    </>
  );
}

export default Navigator;
