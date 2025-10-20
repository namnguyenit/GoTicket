import { Outlet } from "react-router-dom";
import "./App.css";
import { LogOutProvider } from "./context/LogoutProvider";

function App() {
  return (
    <div>
      <LogOutProvider>
        <Outlet />
      </LogOutProvider>
    </div>
  );
}

export default App;
