import { createContext, useState, type JSX } from "react";
const LogOutContext = createContext<any>("");

function LogOutProvider({ children }: { children: JSX.Element }) {
  const [logout, setLogout] = useState<boolean>(false);
  console.log(logout);
  return (
    <LogOutContext.Provider value={{ logout, setLogout }}>
      {children}
    </LogOutContext.Provider>
  );
}

export { LogOutContext, LogOutProvider };
