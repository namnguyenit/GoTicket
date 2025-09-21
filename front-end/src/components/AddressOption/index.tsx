import clsx from "clsx";
import Assets from "../../assets";
import { useState } from "react";
import Select from "../Select";
import {
  Bus,
  Train,
  Circle,
  MapPin,
  CalendarCheck,
  ArrowRightLeft,
} from "lucide-react";
// import style from "./AddressOption.module.css";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";

const initSchedule = {
  from: {
    city: { name: "", value: "" },
    location: { name: "", value: "" },
  },
  to: {
    city: { name: "", value: "" },
    location: { name: "", value: "" },
  },
  date: undefined,
  vehicle: "bus",
};

interface schedule {
  from: {
    city: { name: string; value: string };
    location: { name: string; value: string };
  };
  to: {
    city: { name: string; value: string };
    location: { name: string; value: string };
  };
  date: Date | undefined;
  vehicle: string;
}

function AddressOption() {
  const data = [
    {
      name: "Hà Nội",
      value: "ha-noi",
      location: [
        { name: "Hà Nội 1", value: "ha-noi1" },
        { name: "Hà Nội 2", value: "ha-noi2" },
        { name: "Hà Nội 3", value: "ha-noi3" },
      ],
    },
    {
      name: "Lào Cai",
      value: "lao-cai",
      location: [
        { name: "Lào Cai 1", value: "lao-cai1" },
        { name: "Lào Cai 2", value: "lao-cai2" },
        { name: "Lào Cai 3", value: "lao-cai3" },
      ],
    },
    {
      name: "Lào Cai",
      value: "lao-cai",
      location: [
        { name: "Lào Cai 1", value: "lao-cai1" },
        { name: "Lào Cai 2", value: "lao-cai2" },
        { name: "Lào Cai 3", value: "lao-cai3" },
      ],
    },
    {
      name: "Lào Cai",
      value: "lao-cai",
      location: [
        { name: "Lào Cai 1", value: "lao-cai1" },
        { name: "Lào Cai 2", value: "lao-cai2" },
        { name: "Lào Cai 3", value: "lao-cai3" },
      ],
    },
    {
      name: "Hà Nội",
      value: "ha-noi",
      location: [
        { name: "Hà Nội 1", value: "ha-noi1" },
        { name: "Hà Nội 2", value: "ha-noi2" },
        { name: "Hà Nội 3", value: "ha-noi3" },
      ],
    },
  ];
  const [schedule, setSchedule] = useState<schedule>(initSchedule);
  const [toggleAddress, setToggleAddress] = useState(false);
  const [indexNav, setIndexNav] = useState(0);

  //
  const [open, setOpen] = useState(false);
  const [date, setDate] = useState<Date | undefined>(undefined);

  return (
    <>
      <div className="grid size-full grid-rows-[25%]">
        {/* Vehicle Option Navigator */}
        <div className="relative overflow-hidden rounded-t-2xl bg-[#5B2642]">
          <div className="absolute top-0 left-0 z-1 grid size-full auto-cols-[20%] grid-flow-col">
            <div
              className="flex size-full items-center justify-center gap-[5%]"
              onClick={() => {
                setIndexNav(0);
                setSchedule((prev) => ({ ...prev, vehicle: "bus" }));
              }}
            >
              <div className="">
                <Bus
                  className="transition-colors duration-500"
                  color={indexNav === 0 ? "#5B2642" : "#fff"}
                  strokeWidth={1}
                  size={30}
                />
              </div>
              <div
                className={clsx(
                  "text-xl font-bold transition-colors duration-500",
                  indexNav === 0 ? "text-[#5B2642]" : "text-white",
                )}
              >
                Bus
              </div>
            </div>
            <div
              className="flex items-center justify-center gap-[5%]"
              onClick={() => {
                setIndexNav(1);
                setSchedule((prev) => ({ ...prev, vehicle: "train" }));
              }}
            >
              <div className="">
                <Train
                  className="transition-colors duration-500"
                  color={indexNav === 1 ? "#5B2642" : "#fff"}
                  strokeWidth={1}
                  size={30}
                />
              </div>
              <div
                className={clsx(
                  "text-xl font-bold transition-colors duration-500",
                  indexNav === 1 ? "text-[#5B2642]" : "text-white",
                )}
              >
                Train
              </div>
            </div>
          </div>
          <div
            className={clsx(
              "absolute bottom-0 transition-[left] duration-500",
              indexNav == 0 && "left-[calc(0*20%-(370px-20%)/2)]",
              indexNav == 1 && "left-[calc(1*20%-(370px-20%)/2)]",
            )}
          >
            <Assets.TabIcon />
          </div>
        </div>

        <div className="grid grid-rows-[60%_40%] rounded-b-2xl bg-white">
          {/* Schedule Option */}
          <div className="grid h-[80%] w-[90%] grid-cols-3 self-end justify-self-center">
            {/* From */}
            <div className="relative grid auto-cols-[min-content_1fr] grid-flow-col items-center gap-2.5 rounded-l-2xl bg-[#FFF1E3] outline outline-[#aaa]">
              <div className="absolute top-1.5 left-2.5 text-xs text-[#aaa]">
                Nơi xuất phát
              </div>
              <div className="flex w-7 justify-end">
                <Circle size={15} strokeWidth={3} color="#5B2642" />
              </div>
              <div className="font-bold text-[#5B2642]">
                {schedule.from.city.name
                  ? schedule.from.city.name +
                    " - " +
                    schedule.from.location.name
                  : "Chọn điểm đi"}
              </div>
              <div className="absolute top-0 left-0 z-1 size-full rounded-l-2xl hover:outline-3 hover:outline-[#5b26427e]">
                <Select
                  Item={data}
                  onChange={(e) => {
                    setSchedule((prev) => ({
                      ...prev,
                      from: {
                        city: e.city,
                        location: e.location,
                      },
                    }));
                  }}
                />
              </div>
            </div>
            {/* To */}
            <div className="relative grid auto-cols-[min-content_1fr] grid-flow-col items-center gap-2.5 bg-[#FFF1E3] outline outline-[#aaa]">
              <div
                className={clsx(
                  "absolute top-1/2 left-0 z-2 -translate-1/2 rounded-full bg-white p-2 transition-transform duration-300",
                  toggleAddress ? "rotate-180" : "",
                )}
                onClick={() => {
                  setToggleAddress(!toggleAddress);
                }}
              >
                <ArrowRightLeft size={20} />
              </div>
              <div className="absolute top-1.5 left-2.5 text-xs text-[#aaa]">
                Nơi đến
              </div>
              <div className="flex w-10 justify-end">
                <MapPin size={15} strokeWidth={3} color="#5B2642" />
              </div>
              <div className="font-bold text-[#5B2642]">
                {schedule.to.city.name
                  ? schedule.to.city.name + " - " + schedule.to.location.name
                  : "Chọn điểm đến"}
              </div>
              <div className="absolute top-0 left-0 z-1 size-full hover:outline-3 hover:outline-[#5b26427e]">
                <Select
                  Item={data}
                  onChange={(e) => {
                    setSchedule((prev) => ({
                      ...prev,
                      to: {
                        city: e.city,
                        location: e.location,
                      },
                    }));
                  }}
                />
              </div>
            </div>
            {/* Calender */}
            <div className="relative grid auto-cols-[min-content_1fr] grid-flow-col items-center gap-2.5 rounded-r-2xl bg-[#FFF1E3] outline outline-[#aaa]">
              <div className="absolute top-1.5 left-2.5 text-xs text-[#aaa]">
                Lịch xuất phát
              </div>
              <div className="flex w-7 justify-end">
                <CalendarCheck size={15} strokeWidth={3} color="#5B2642" />
              </div>
              {!date && (
                <div className="font-bold text-[#5B2642]">Chọn lịch</div>
              )}
              <div className="absolute top-0 left-0 z-1 size-full rounded-r-2xl hover:outline-3 hover:outline-[#5b26427e]">
                <div className="flex size-full flex-col gap-3">
                  <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        id="date"
                        className="ml-6 size-full justify-between border-0 bg-[transparent] text-base font-bold text-[#5B2642] hover:bg-[transparent] hover:text-[#5B2642]"
                      >
                        {date ? date.toLocaleDateString() : ""}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent
                      className="w-auto overflow-hidden bg-[#fff9f3] p-0"
                      align="start"
                    >
                      <Calendar
                        mode="single"
                        selected={date}
                        captionLayout="dropdown"
                        onSelect={(date) => {
                          setDate(date);
                          setOpen(false);
                          setSchedule((prev) => ({ ...prev, date: date }));
                        }}
                      />
                    </PopoverContent>
                  </Popover>
                </div>
              </div>
            </div>
          </div>
          <div className="mr-[5%] flex h-1/2 w-1/8 items-center justify-center self-center justify-self-end rounded-full bg-[#F7AC3D] text-sm font-bold text-white transition-colors duration-500 hover:bg-[#5b2642]">
            Tìm kiếm
          </div>
        </div>
      </div>
    </>
  );
}

export default AddressOption;
