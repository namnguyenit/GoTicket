import { createBrowserRouter } from "react-router-dom";

import App from "../App";
import Main from "../layout/Main";
import Body from "../layout/Main/Body";
import Home from "../page/Home";
import Book from "../page/Book";
import Signin from "@/page/Signin";
import Signup from "@/page/Signup";

const router = createBrowserRouter([
  {
    path: "/",
    element: <App />,
    children: [
      {
        element: <Main />,
        children: [
          {
            element: <Body />,
            children: [
              { index: true, element: <Home /> },
              { path: "/book", element: <Book /> },
              { path: "/sign-in", element: <Signin /> },
              { path: "/sign-up", element: <Signup /> },
            ],
          },
        ],
      },
    ],
  },
]);

export default router;
