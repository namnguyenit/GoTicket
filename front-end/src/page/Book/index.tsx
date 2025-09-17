import AddressOption from "../../components/AddressOption";
import Assets from "../../assets";

function Book() {
  return (
    <>
      <div className="flex h-[calc(100vh*3)] w-screen flex-col items-center">
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
        {/*  Trip */}
        <div className="mt-25 grid h-[1000px] w-[80%] grid-cols-[1fr_3fr] gap-8">
          {/* Filters */}
          <div className="grid grid-cols-1 content-start">
            <div className="flex h-14 items-center justify-center bg-[#57112f] text-3xl font-bold text-white">
              Filters
            </div>
            {/* Filter for ticket cost */}
            <div className="dash-bottom grid h-52 grid-rows-[30%_50%] content-evenly bg-white">
              <div className="dash-bottom flex h-4/5 w-4/5 items-center self-center justify-self-center text-xl font-bold text-[#57112f]">
                Giá vé
              </div>
              <div className="self-center justify-self-center">Thêm gì?</div>
            </div>
            <div className="dash-bottom grid h-[400px] grid-rows-[15%_60%] content-evenly bg-white">
              <div className="dash-bottom flex h-4/5 w-4/5 items-center self-center justify-self-center text-xl font-bold text-[#57112f]">
                Thời gian
              </div>
              <div className="grid w-4/5 grid-rows-4 justify-self-center">
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Assets.Sunrise color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    00:00 - 5:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Assets.Sunny color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    06:00 - 11:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Assets.Sunset color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    12:00 - 17:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="flex h-6 w-6 justify-center">
                    <Assets.Moon color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    18:00 - 23:59
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/* Ticket */}
          <div className="bg-green-300"></div>
        </div>
      </div>
    </>
  );
}

export default Book;
