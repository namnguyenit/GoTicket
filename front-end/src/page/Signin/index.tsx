import clsx from "clsx";
import styles from "./Signin.module.css";
import { Eye, LockKeyholeIcon, UserRound } from "lucide-react";
import { useEffect, useState } from "react";
import { useFetch } from "@/hooks/useFetch";
import { useNavigate } from "react-router-dom";
import { URL } from "@/config";

interface SigninType {
  email: string | null;
  password: string | null;
}
const initSign: SigninType = {
  email: null,
  password: null,
};

function Signin() {
  const [info, setInfo] = useState(initSign);
  const navigate = useNavigate();

  const {
    data,
    loading,
    error,
    get,
    post,
    put,
    delete: del,
  } = useFetch<SigninType[]>(URL);

  const handleAdd = () => {
    post("/api/auth/login", info, {
      Authorization: "Bearer your_token_here",
    });
  };

  console.log(info);

  // console.log(data, error);
  // if (data?.success) {
  //   navigate("/", { replace: true });
  // }
  useEffect(() => {
    if (data?.success) {
      navigate("/", { replace: true });
      localStorage.setItem("goticketToken", data.token);
    }
  }, [data]);

  return (
    <>
      <div className="h-[200vh] w-full">
        <div className="after-overlay relative h-[30%] w-full bg-[url(/book-page-bg.jpg)] bg-cover bg-center">
          <div className="absolute bottom-1/2 left-1/2 z-10 grid h-1/3 w-1/2 -translate-x-1/2 translate-y-3/8 grid-rows-1 items-center text-center">
            <div className="text-6xl font-bold text-white">
              Đăng nhập - Đăng kí
            </div>
          </div>
        </div>
        <div className="flex h-[70%] w-full items-center justify-center">
          <div className={styles.frameParent}>
            <div className={styles.choMngTrLiParent}>
              <div className={styles.choMngTr}>Chào mừng trở lại</div>
              <div className={styles.ngNhp}>Đăng nhập để tiếp tục</div>
            </div>
            <div className={styles.frameGroup}>
              <div className={styles.inputParent}>
                <div className={styles.lucideuserRoundParent}>
                  <UserRound />
                  <div className={styles.tiKhon}>Tài khoản / Email</div>
                </div>
                <input
                  type="text"
                  name="email"
                  placeholder="Tài khoản ..."
                  className={clsx(styles.input, "px-6")}
                  onChange={(e) => {
                    setInfo((prev) => ({ ...prev, email: e.target.value }));
                  }}
                />
              </div>
              <div className={styles.frameContainer}>
                <div className={styles.lucidelockKeyholeParent}>
                  <LockKeyholeIcon />
                  <div className={styles.tiKhon}>Mật khẩu</div>
                </div>
                <div className={styles.inputGroup}>
                  <Eye className="absolute top-1/2 right-0 h-8 w-8 -translate-1/2" />
                  <input
                    type="password"
                    name=""
                    placeholder="Mật khẩu ..."
                    className={clsx(styles.input2, "px-6")}
                    onChange={(e) => {
                      setInfo((prev) => ({
                        ...prev,
                        password: e.target.value,
                      }));
                    }}
                  />
                </div>
              </div>
              <div className={styles.qunMtKhu}>
                <div className={styles.qunMtKhu2}>Quên mật khẩu?</div>
              </div>
            </div>
            <div
              className={clsx(styles.button, "hover:bg-black")}
              onClick={() => {
                handleAdd();
              }}
            >
              <b className={styles.submit}>Đăng nhập</b>
            </div>
            <div className={styles.ngKNgayParent}>
              <div className={styles.ngKNgay}> Đăng kí ngay</div>
              <div className={styles.chaCTi}>Chưa có tài khoản?</div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default Signin;
