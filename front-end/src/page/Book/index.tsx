import AddressOption from "../../components/AddressOption";

function Book() {
  return (
    <>
      <div className="h-[calc(100vh*3)] w-screen">
        <div className="after-overlay relative h-[450px] w-full bg-[url(/book-page-bg.jpg)] bg-cover bg-center">
          <div className="absolute bottom-1/2 left-1/2 z-10 grid h-1/3 w-1/3 -translate-x-1/2 translate-y-3/8 grid-rows-1 items-center text-center">
            <div className="text-6xl font-bold text-white">Booking List</div>
          </div>
        </div>
        <div className="relative h-[200px] w-full bg-white">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <AddressOption />
          </div>
        </div>
        {/* Trip */}
        <div className=""></div>
      </div>
    </>
  );
}

export default Book;
