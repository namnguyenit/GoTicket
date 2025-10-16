import { useLocation, useNavigate } from "react-router-dom";
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

function Book() {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const json = JSON.parse(decodeURIComponent(params.get("data") || "null"));

  const { data, loading, error, get } = useFetch(URL);
  const [searchQuery, setSearchQuery] = useState<string | null>(null);

  useEffect(() => {
    const date = json?.date
      ? new Date(json.date).toLocaleDateString("en-CA", {
          timeZone: "Asia/Ho_Chi_Minh",
        })
      : null;

    // const params = encodeURIComponent(
    //   `origin_location=${json.region.from.name}&destination_location=${json.region.to.name}&date=${date}&vehicle_type=${json.vehicle}`,
    // );

    const query = json?.region
      ? `origin_location=${json.region.from.name}&destination_location=${json.region.to.name}&date=${date}&vehicle_type=${json.vehicle}`
      : null;
    if (query) {
      setSearchQuery(query);
      get(`/api/trips/search?${query}`); // gọi API khi component mount
    }
  }, [location]);

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
            {error && (
              <div
                className="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                role="alert"
                aria-live="polite"
              >
                Không tải được danh sách chuyến. Vui lòng thử lại.
                <button
                  className="ml-3 rounded-md bg-[#F7AC3D] px-3 py-1 text-white hover:bg-[#6a314b]"
                  onClick={() =>
                    searchQuery && get(`/api/trips/search?${searchQuery}`)
                  }
                >
                  Thử lại
                </button>
              </div>
            )}

            {loading && (
              <div className="space-y-4">
                {[1, 2, 3].map((i) => (
                  <div
                    key={i}
                    className="grid h-[180px] animate-pulse grid-cols-[20%_50%_30%] rounded-2xl bg-white shadow-sm"
                  >
                    <div className="flex flex-col items-center justify-evenly p-4">
                      <div className="h-24 w-2/3 rounded bg-gray-200" />
                      <div className="mt-2 h-4 w-3/4 rounded bg-gray-200" />
                    </div>
                    <div className="grid grid-rows-[40%_40%] content-evenly p-4">
                      <div className="space-y-2">
                        <div className="h-5 w-2/3 rounded bg-gray-200" />
                        <div className="h-4 w-1/2 rounded bg-gray-200" />
                      </div>
                      <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-2">
                          <div className="h-5 w-1/2 rounded bg-gray-200" />
                          <div className="h-4 w-2/3 rounded bg-gray-200" />
                        </div>
                        <div className="space-y-2">
                          <div className="h-5 w-1/3 rounded bg-gray-200" />
                          <div className="h-4 w-1/2 rounded bg-gray-200" />
                        </div>
                      </div>
                    </div>
                    <div className="flex flex-col items-center justify-evenly border-l-2 p-4">
                      <div className="h-6 w-24 rounded bg-gray-200" />
                      <div className="h-8 w-24 rounded bg-gray-200" />
                    </div>
                  </div>
                ))}
              </div>
            )}

            {!loading &&
              !error &&
              data &&
              data.data &&
              data.data.length > 0 && (
                <>
                  {data.data.map((item: any, index: any) => (
                    <Ticket data={item} key={index} />
                  ))}
                </>
              )}

            {!loading &&
              !error &&
              (!data || !data.data || data.data.length === 0) && (
                <div className="rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                  Không có chuyến phù hợp với tiêu chí tìm kiếm.
                </div>
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
interface initTrip {
  tripID: number | null;
  seats: { id: number; seat_number: string; status: string; price: string }[];
  from: string;
  to: string;
  totalPrice: number;
}

function Ticket({ data }: Ticket) {
  const navigate = useNavigate();

  const [book, setBook] = useState<boolean>(false);

  const { data: seatDatas, loading: seatLoading, error, get } = useFetch(URL);

  useEffect(() => {
    book && get(`/api/trips/${data.id}`);
    // book && console.log(seatDatas);
  }, [book]);

  const [initTrip, setInitTrip] = useState<initTrip>({
    tripID: data.id,
    seats: [],
    from: "",
    to: "",
    totalPrice: 0,
  });

  useEffect(() => {
    setInitTrip((prev) => ({
      ...prev,
      seats: seatDatas?.data.coaches[0].seats || [],
      totalPrice: 0,
    }));
  }, [seatDatas]);

  // console.log(initTrip);

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
              {!seatLoading && seatDatas && !error ? (
                <>
                  {/* Tầng 1 */}
                  <div className="flex w-[40%] flex-col items-center justify-evenly">
                    <div className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#6a314b] text-white">
                      Tầng 1
                    </div>
                    <div className="grid w-full grid-flow-col grid-cols-[50px_50px_50px] grid-rows-[repeat(7,30px)] justify-between gap-[6px]">
                      {/* Bỏ */}
                      <div className="col-start-2 row-start-7"></div>
                      {/* Dải Ghế */}
                      {initTrip.seats
                        .slice(0, initTrip.seats.length / 2)
                        .map((item, index) => (
                          <div
                            className={clsx(
                              "flex h-[30px] items-center justify-center rounded-full text-sm hover:outline-2 hover:outline-[#6a314b7d]",
                              item.status == "available" && "bg-green-500",
                              item.status == "temp" && "bg-gray-300",
                              item.status == "booked" && "bg-red-500",
                            )}
                            key={index}
                            onClick={() => {
                              setInitTrip((prev) => {
                                const findSeat = prev.seats.findIndex(
                                  (seat) => seat.id == item.id,
                                );

                                const updateSeats = [...prev.seats].map(
                                  (item) => ({
                                    ...item,
                                  }),
                                );
                                if (
                                  updateSeats[findSeat].status == "available"
                                ) {
                                  updateSeats[findSeat].status = "temp";
                                } else if (
                                  updateSeats[findSeat].status == "temp"
                                ) {
                                  updateSeats[findSeat].status = "available";
                                }

                                return {
                                  ...prev,
                                  seats: updateSeats,
                                  totalPrice: updateSeats.reduce((acc, cur) => {
                                    if (cur.status == "temp") {
                                      acc += parseInt(cur.price);
                                    }
                                    return acc;
                                  }, 0),
                                };
                              });
                            }}
                          >
                            {item.seat_number}
                          </div>
                        ))}
                    </div>
                  </div>
                  {/* Tầng 2 */}
                  <div className="flex w-[40%] flex-col items-center justify-evenly">
                    <div className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#6a314b] text-white">
                      Tầng 2
                    </div>
                    <div className="grid w-full grid-flow-col grid-cols-[50px_50px_50px] grid-rows-[repeat(7,30px)] justify-between gap-[6px]">
                      {/* Bỏ */}
                      <div className="col-start-2 row-start-7"></div>
                      {/* Dải Ghế */}
                      {initTrip.seats
                        .slice(initTrip.seats.length / 2)
                        .map((item, index) => (
                          <div
                            className={clsx(
                              "flex h-[30px] items-center justify-center rounded-full text-sm hover:outline-2 hover:outline-[#6a314b7d]",
                              item.status == "available" && "bg-green-500",
                              item.status == "temp" && "bg-gray-300",
                              item.status == "booked" && "bg-red-500",
                            )}
                            key={index}
                            onClick={() => {
                              setInitTrip((prev) => {
                                const findSeat = prev.seats.findIndex(
                                  (seat) => seat.id == item.id,
                                );

                                const updateSeats = [...prev.seats].map(
                                  (item) => ({
                                    ...item,
                                  }),
                                );
                                if (
                                  updateSeats[findSeat].status == "available"
                                ) {
                                  updateSeats[findSeat].status = "temp";
                                } else if (
                                  updateSeats[findSeat].status == "temp"
                                ) {
                                  updateSeats[findSeat].status = "available";
                                }

                                return {
                                  ...prev,
                                  seats: updateSeats,
                                  totalPrice: updateSeats.reduce((acc, cur) => {
                                    if (cur.status == "temp") {
                                      acc += parseInt(cur.price);
                                    }
                                    return acc;
                                  }, 0),
                                };
                              });
                            }}
                          >
                            {item.seat_number}
                          </div>
                        ))}
                    </div>
                  </div>
                </>
              ) : seatLoading ? (
                <div className="flex size-full flex-col items-center justify-center text-gray-400">
                  <div className="h-6 w-6 animate-spin rounded-full border-2 border-gray-300 border-t-[#6a314b]" />
                  <div className="mt-2 text-sm text-[#6a314b]">
                    Đang tải sơ đồ ghế...
                  </div>
                </div>
              ) : error ? (
                <div className="flex size-full flex-col items-center justify-center">
                  <div
                    className="rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700"
                    role="alert"
                    aria-live="polite"
                  >
                    Không tải được dữ liệu ghế. Vui lòng thử lại.
                  </div>
                  <button
                    className="mt-3 rounded-md bg-[#F7AC3D] px-3 py-1 text-white hover:bg-[#6a314b]"
                    onClick={() => get(`/api/trips/${data.id}`)}
                  >
                    Thử lại
                  </button>
                </div>
              ) : null}
            </div>

            {/* Status */}
            <div className="flex w-[85%] gap-5">
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-green-500"></div>
                <div className="text-xs text-[#555]">Trống</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-yellow-400"></div>
                <div className="text-xs text-[#555]">Giữ</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-red-500"></div>
                <div className="text-xs text-[#555]">Bán</div>
              </div>
              <div className="flex items-center gap-1">
                <div className="h-[15px] w-[25px] bg-gray-300"></div>
                <div className="text-xs text-[#555]">Chọn</div>
              </div>
            </div>
            {/* Submit */}
            <div className="flex w-[80%] items-center justify-between">
              <div className="text-xs">
                <div className="flex items-center gap-2">
                  <div className="text-[#555]">Ghế đã chọn: </div>
                  <div className="">
                    {initTrip.seats
                      .reduce((acc, cur) => {
                        if (cur.status == "temp") {
                          acc += cur.seat_number + ",";
                        }
                        return acc;
                      }, "")
                      .slice(0, -1)}
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <div className="text-[#555]">Tổng tiền</div>
                  <div className="">
                    {initTrip.totalPrice.toLocaleString()}
                    <span>đ</span>
                  </div>
                </div>
              </div>
              <div
                className="flex h-[40px] w-[120px] items-center justify-center rounded-full bg-[#F7AC3D] font-bold transition-colors duration-500 hover:bg-[#6a314b] hover:text-white"
                onClick={() => {
                  navigate("/check-out", { state: initTrip });
                }}
              >
                Chọn
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
