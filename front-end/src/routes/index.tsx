import { createBrowserRouter } from "react-router-dom";

import App from "../App";
import Main from "../layout/Main";
import Body from "../layout/Main/Body";
import Home from "../page/Home";
import Book from "../page/Book";

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
            ],
          },
        ],
      },
    ],
  },
]);

export default router;
