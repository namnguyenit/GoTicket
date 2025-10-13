import { useLocation } from "react-router-dom";
import { useEffect, useState } from "react";
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
import clsx from "clsx";
import { URL } from "@/config";
import { useFetch } from "@/hooks/useFetch";

const Tickets = [
  {
    id: 43,
    trip: "Tuyến Hà Nội - TP. Hồ Chí Minh",
    imageLink: "trip-logo.png",
    pickTake: "Điểm a - b",
    departureDate: new Date("2025-10-09T12:48:07.000Z"),
    emptyNumber: 54,
    vendorName: "Phương Trang",
    vendorType: "bus",
    price: 34455666734,
  },
  {
    id: 44,
    trip: "Tuyến Đà Nẵng - Nha Trang",
    imageLink: "trip-logo.png",
    pickTake: "Điểm c - d",
    departureDate: new Date("2025-10-10T07:30:00.000Z"),
    emptyNumber: 23,
    vendorName: "Hoàng Long",
    vendorType: "bus",
    price: 500000,
  },
  {
    id: 45,
    trip: "Tuyến Cần Thơ - Hà Nội",
    imageLink: "trip-logo.png",
    pickTake: "Điểm e - f",
    departureDate: new Date("2025-10-11T19:45:00.000Z"),
    emptyNumber: 10,
    vendorName: "Mai Linh",
    vendorType: "bus",
    price: 750000,
  },
  {
    id: 46,
    trip: "Tuyến Hải Phòng - Quảng Ninh",
    imageLink: "trip-logo.png",
    pickTake: "Điểm g - h",
    departureDate: new Date("2025-10-12T06:15:00.000Z"),
    emptyNumber: 32,
    vendorName: "Kumho Việt Thanh",
    vendorType: "bus",
    price: 220000,
  },
  {
    id: 47,
    trip: "Tuyến Huế - Đà Lạt",
    imageLink: "trip-logo.png",
    pickTake: "Điểm i - j",
    departureDate: new Date("2025-10-13T21:00:00.000Z"),
    emptyNumber: 5,
    vendorName: "Thành Bưởi",
    vendorType: "bus",
    price: 650000,
  },
  {
    id: 48,
    trip: "Tuyến TP. Hồ Chí Minh - Vũng Tàu",
    imageLink: "trip-logo.png",
    pickTake: "Điểm k - l",
    departureDate: new Date("2025-10-14T09:00:00.000Z"),
    emptyNumber: 40,
    vendorName: "Phương Trang",
    vendorType: "bus",
    price: 180000,
  },
  {
    id: 49,
    trip: "Tuyến Hà Nội - Lào Cai",
    imageLink: "trip-logo.png",
    pickTake: "Điểm m - n",
    departureDate: new Date("2025-10-15T22:15:00.000Z"),
    emptyNumber: 16,
    vendorName: "Sapa Express",
    vendorType: "bus",
    price: 400000,
  },
  {
    id: 50,
    trip: "Tuyến Biên Hòa - Đà Nẵng",
    imageLink: "trip-logo.png",
    pickTake: "Điểm o - p",
    departureDate: new Date("2025-10-16T08:00:00.000Z"),
    emptyNumber: 27,
    vendorName: "Hoàng Hà",
    vendorType: "bus",
    price: 720000,
  },
];

function Book() {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const json = JSON.parse(decodeURIComponent(params.get("data") || "null"));

  const { data, loading, error, get } = useFetch(URL);

  useEffect(() => {
    const date = json?.date
      ? new Date(json.date).toLocaleDateString("en-CA", {
          timeZone: "Asia/Ho_Chi_Minh",
        })
      : null;

    // const params = encodeURIComponent(
    //   `origin_location=${json.region.from.name}&destination_location=${json.region.to.name}&date=${date}&vehicle_type=${json.vehicle}`,
    // );

    const params = json?.region
      ? `origin_location=${json.region.from.name}&destination_location=${json.region.to.name}&date=${date}&vehicle_type=${json.vehicle}`
      : null;
    if (json?.region) {
      get(`/api/trips/search?${params}`); // gọi API khi component mount
    }

    console.log(params);
  }, [location]);
  console.log(data);
  return (
    <>
      <div className="flex w-screen flex-col items-center">
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
        <div className="my-25 grid w-[70%] grid-cols-[1fr_3fr] gap-8">
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
          <div>
            {data && data.data ? (
              data.data.map((item: any) => <Ticket data={item} key={item.id} />)
            ) : (
              <></>
            )}
          </div>
        </div>
      </div>
    </>
  );
}

export default Book;

interface Ticket {
  data: {
    id: number; // 43
    trip: string; // Tuyến Hà Nội - TP. Hồ Chí Minh
    imageLink: string; // dhgkdbgkbkcxbkk
    pickTake: string; // Điểm đón trả
    departureDate: Date; // Ngày suất phát
    emptyNumber: number; // số ghế còn trống
    vendorName: string; // Tên xe
    vendorType: string; // Loại xe Bus | Train
    price: number; // 34455666734
  };
}

function Ticket({ data }: Ticket) {
  const [book, setBook] = useState<boolean>(false);

  return (
    <>
      <div className="mb-5">
        {/* Ticket */}
        <div className="mb-5 grid h-[180px] grid-cols-[20%_50%_30%] rounded-2xl bg-white shadow-sm">
          {/* Logo */}
          <div className="flex flex-col items-center justify-evenly">
            <img
              className="w-2/3 rounded-sm object-cover object-center"
              src={data.imageLink || "trip-logo.png"}
              alt="Trip Logo"
            />
            <div className="flex w-[80%] text-center text-sm font-bold text-[#6a314b]">
              {data.vendorName}
            </div>
          </div>
          {/* Info */}
          <div className="grid grid-rows-[40%_40%] content-evenly">
            <div className="flex flex-col justify-evenly">
              <div className="text-lg font-bold text-[#6a314b]">
                {data.trip}
              </div>
              <div className="flex">
                <MapPin className="mr-2" color="#aaa" />
                <div className="text-[#aaa]">
                  {data.pickTake || "Điểm đón - trả"}
                </div>
              </div>
            </div>
            <div className="grid grid-cols-[45%_45%] justify-between">
              <div className="flex flex-col justify-evenly">
                <div className="flex">
                  <Clock color="#6a314b" className="mr-2" />
                  <div className="text-lg font-bold text-[#6a314b]">
                    {data.departureDate
                      ? new Date(data.departureDate).toLocaleTimeString(
                          "en-GB",
                          {
                            timeZone: "Asia/Ho_Chi_Minh",
                          },
                        )
                      : undefined}
                  </div>
                </div>
                <div className="text-[#aaa]">
                  <span className="mr-1.5">Thời gian:</span>
                  {data.departureDate
                    ? new Date(data.departureDate).toLocaleDateString("en-CA", {
                        timeZone: "Asia/Ho_Chi_Minh",
                      })
                    : undefined}
                </div>
              </div>
              <div className="flex flex-col justify-evenly">
                <div className="flex items-center">
                  <div className="mr-2 text-lg font-bold text-[#F7AC3D]">
                    {data.emptyNumber}
                  </div>
                  <div className="font-bold">chỗ trống</div>
                </div>
                <div className="flex">
                  <BusFront color="#6a314b" className="mr-2" />
                  <div className="text-[#aaa]">{data.vendorType}</div>
                </div>
              </div>
            </div>
          </div>
          {/* Price */}
          <div className="flex flex-col items-center justify-evenly border-l-2">
            <div className="text-2xl font-bold text-red-500">
              {data.price ? data.price.toLocaleString("vi-VN") : null} đ
            </div>
            <div
              className="flex h-2/10 w-1/2 items-center justify-center rounded-2xl bg-[#F7AC3D] font-bold text-white transition-colors duration-500 hover:bg-[#6a314b]"
              onClick={() => {
                setBook(!book);
              }}
            >
              Đặt Vé
            </div>
          </div>
        </div>
        {/* Seat */}
        <div
          className={clsx(
            "relative overflow-hidden rounded-2xl bg-white shadow-sm transition-[height] duration-500 ease-in",
            book ? "h-[450px]" : "h-0",
          )}
        >
          <div className="absolute top-0 left-0 grid h-[450px] w-full grid-rows-[70%_10%_15%] content-center justify-items-center">
            {/* Hai tầng */}
            <div className="flex w-[80%] justify-between">
              {/* Tầng 1 */}
              <div className="flex w-[40%] flex-col items-center justify-evenly">
                <div className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#6a314b] text-white">
                  Tầng 1
                </div>
                <div className="grid w-full grid-cols-[50px_50px_50px] grid-rows-[repeat(7,30px)] justify-between gap-[6px]">
                  {/* Bỏ */}
                  <div className="col-start-2 row-start-7"></div>
                  {/* Dải Ghế */}
                  {new Array(20).fill(0).map((item) => (
                    <div className="flex h-[30px] items-center justify-center rounded-full bg-green-500 text-sm hover:outline-2 hover:outline-[#6a314b7d]">
                      B1
                    </div>
                  ))}
                </div>
              </div>
              {/* Tầng 2 */}
              <div className="flex w-[40%] flex-col items-center justify-evenly">
                <div className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#6a314b] text-white">
                  Tầng 2
                </div>
                <div className="grid w-full grid-cols-[50px_50px_50px] grid-rows-[repeat(7,30px)] justify-between gap-[6px]">
                  {/* Bỏ */}
                  <div className="col-start-2 row-start-7"></div>
                  {/* Dải Ghế */}
                  {new Array(20).fill(0).map((item) => (
                    <div className="flex h-[30px] items-center justify-center rounded-full bg-[#BDD6FC] text-sm hover:outline-2 hover:outline-[#6a314b7d]">
                      B1
                    </div>
                  ))}
                </div>
              </div>
            </div>
            {/* Status */}
            <div className="flex w-[85%] gap-5">
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-green-500"></div>
                <div className="text-xs text-[#555]">Trống</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-yellow-400"></div>
                <div className="text-xs text-[#555]">Đặt</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-red-500"></div>
                <div className="text-xs text-[#555]">Bán</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-gray-300"></div>
                <div className="text-xs text-[#555]">Giữ</div>
              </div>
            </div>
            {/* Submit */}
            <div className="flex w-[80%] items-center justify-between">
              <div className="text-xs">
                <div className="flex items-center gap-2">
                  <div className="text-[#555]">Ghế đã chọn: </div>
                  <div className="">E1 E2</div>
                </div>
                <div className="flex items-center gap-2">
                  <div className="text-[#555]">Tổng tiền</div>
                  <div className="">
                    360.000<span>đ</span>
                  </div>
                </div>
              </div>
              <div className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#F7AC3D] font-bold transition-colors duration-500 hover:bg-[#6a314b] hover:text-white">
                Chọn
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
