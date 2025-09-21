import AddressOption from "../../components/AddressOption";
import {
  Sunrise,
  Sun,
  Sunset,
  MoonStar,
  MapPin,
  Clock,
  BusFront,
} from "lucide-react";

function Book() {
  return (
    <>
      <div className="flex h-[calc(100vh*3)] w-screen flex-col items-center">
        <div className="after-overlay relative h-[450px] w-full bg-[url(/book-page-bg.jpg)] bg-cover bg-center">
          <div className="absolute bottom-1/2 left-1/2 z-10 grid h-1/3 w-1/3 -translate-x-1/2 translate-y-3/8 grid-rows-1 items-center text-center">
            <div className="text-6xl font-bold text-white">Booking List</div>
          </div>
        </div>
        <div className="relative h-[10vh] w-[70vw]">
          <div className="absolute left-[50%] h-[35vh] w-[70vw] translate-x-[-50%] translate-y-[-150px]">
            <AddressOption />
          </div>
        </div>
        {/*  Trip */}
        <div className="mt-25 grid h-[1000px] w-[70%] grid-cols-[1fr_3fr] gap-8">
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
              <div className="self-center justify-self-center">Giá?</div>
            </div>
            <div className="dash-bottom grid h-[400px] grid-rows-[15%_60%] content-evenly bg-white">
              <div className="dash-bottom flex h-4/5 w-4/5 items-center self-center justify-self-center text-xl font-bold text-[#57112f]">
                Thời gian
              </div>
              <div className="grid w-4/5 grid-rows-4 justify-self-center">
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Sunrise color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    00:00 - 5:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Sun color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    06:00 - 11:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="h-6 w-6">
                    <Sunset color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    12:00 - 17:59
                  </div>
                </div>
                <div className="hover-scale grid h-12 w-full grid-cols-[24px_60%] items-center justify-center gap-6 bg-[#ebebee]">
                  <div className="flex h-6 w-6 justify-center">
                    <MoonStar color="#57112f" />
                  </div>
                  <div className="ml-1 font-bold text-[#57112f]">
                    18:00 - 23:59
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/* Tickets */}
          <div className="grid auto-rows-[180px] gap-5">
            <Ticket />
            <Ticket />
            <Ticket />
            <Ticket />
          </div>
        </div>
      </div>
    </>
  );
}

export default Book;

function Ticket() {
  return (
    <>
      <div className="grid size-full grid-cols-[20%_50%_30%] rounded-2xl bg-white shadow-sm">
        {/* Logo */}
        <div className="flex flex-col items-center justify-evenly">
          <img
            className="w-1/2 object-cover object-center"
            src="trip-logo.png"
            alt="Trip Logo"
          />
          <div className="text-lg font-bold text-[#6a314b]">Văn Minh</div>
        </div>
        {/* Info */}
        <div className="grid grid-rows-[40%_40%] content-evenly">
          <div className="flex flex-col justify-evenly">
            <div className="text-lg font-bold text-[#6a314b]">
              Bx Hà Tĩnh - Bx Nước Ngầm
            </div>
            <div className="flex">
              <MapPin className="mr-2" color="#aaa" />
              <div className="text-[#aaa]">Điểm đón trả</div>
            </div>
          </div>
          <div className="grid grid-cols-[45%_45%] justify-between">
            <div className="flex flex-col justify-evenly">
              <div className="flex">
                <Clock color="#6a314b" className="mr-2" />
                <div className="text-lg font-bold text-[#6a314b]">
                  {"6:30 - 12:30"}
                </div>
              </div>
              <div className="text-[#aaa]">Thời gian: {"6 giờ 10 phút"}</div>
            </div>
            <div className="flex flex-col justify-evenly">
              <div className="flex items-center">
                <div className="mr-2 text-lg font-bold text-[#F7AC3D]">
                  25/30
                </div>
                <div className="font-bold">chỗ trống</div>
              </div>
              <div className="flex">
                <BusFront color="#6a314b" className="mr-2" />
                <div className="text-[#aaa]">xe giường nằm</div>
              </div>
            </div>
          </div>
        </div>
        {/* Price */}
        <div className="flex flex-col items-center justify-evenly border-l-2">
          <div className="text-2xl font-bold text-red-500">200,000đ</div>
          <div className="flex h-2/10 w-1/2 items-center justify-center rounded-2xl bg-[#F7AC3D] font-bold text-white transition-colors duration-500 hover:bg-[#6a314b]">
            Đặt Vé
          </div>
        </div>
      </div>
    </>
  );
}
