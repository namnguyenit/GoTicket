import clsx from "clsx";
import Assets from "../../assets";
import { useState, type JSX } from "react";
import Select from "../Select";

const initSchedule = {
  from: {
    city: { name: "", value: "" },
    location: { name: "", value: "" },
  },
  to: {
    city: { name: "", value: "" },
    location: { name: "", value: "" },
  },
  date: "",
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
  date: string;
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
  console.log(schedule);
  return (
    <>
      <div className="grid h-[40vh] w-[83vw] grid-rows-6 gap-2">
        {/* Vehicle Navigatior */}
        <div
          className={clsx(
            "flex h-[100%] w-[85%] items-center justify-self-center",
          )}
        >
          <VehicleItem Icon={<Assets.Bus color="#fff" />} label="Coach" />
          <VehicleItem Icon={<Assets.Train color="#fff" />} label="Train" />
          <VehicleItem Icon={<Assets.Plain color="#fff" />} label="Flight" />
        </div>
        {/* Bảng tìm chuyến xe*/}
        <div
          className={clsx(
            "row-span-5 grid h-[100%] w-[95%] grid-rows-3 justify-self-center rounded-2xl bg-white",
          )}
        >
          {/* OptionItem */}
          <div
            className={clsx(
              "grid h-[55%] w-[100%] grid-cols-3 justify-items-center overflow-hidden rounded-t-2xl bg-[#622243]",
            )}
          >
            <OptionItem
              Icon={<Assets.Search color="#622243" />}
              label="Booking"
              color="text-[#622243]"
              bg="bg-[#fff]"
            />
            <OptionItem
              Icon={<Assets.CheckList color="#fff" />}
              label="My Trip"
            />
            <OptionItem
              Icon={<Assets.Location color="#fff" />}
              label="Status"
            />
          </div>
          {/* Chọn địa chỉ và ngày */}
          <div className="grid h-[100%] w-[90%] grid-cols-3 justify-self-center rounded-md bg-[#f8f3e7]">
            <div className="h-full w-full rounded-l-md outline-1 outline-[#c2c2c2]">
              <Select
                Item={data}
                onChange={(e) => {
                  setSchedule((prev) => ({
                    ...prev,
                    from: {
                      city: { name: e.city.name, value: e.city.value },
                      location: {
                        name: e.location.name,
                        value: e.location.value,
                      },
                    },
                  }));
                }}
                title={
                  schedule.from.city.name
                    ? schedule.from.city.name +
                      " - " +
                      schedule.from.location.name
                    : "From ..."
                }
              />
            </div>
            <div className="h-full w-full outline-1 outline-[#c2c2c2]">
              <Select
                Item={data}
                onChange={(e) => {
                  setSchedule((prev) => ({
                    ...prev,
                    to: {
                      city: { name: e.city.name, value: e.city.value },
                      location: {
                        name: e.location.name,
                        value: e.location.value,
                      },
                    },
                  }));
                }}
                title={
                  schedule.to.city.name
                    ? schedule.to.city.name + " - " + schedule.to.location.name
                    : "To ..."
                }
              />
            </div>
            <div className="grid grid-cols-[85%] justify-center rounded-r-md font-bold text-[#622243] placeholder-[#622243] outline-1 outline-[#c2c2c2]">
              <input
                className="focus:outline-0"
                type="date"
                name="date"
                onChange={(e) => {
                  setSchedule((prev) => ({ ...prev, date: e.target.value }));
                }}
              />
            </div>
          </div>
          {/* SearchButton */}
          <div className="mr-[5%] flex h-10 w-36 items-center justify-center self-center justify-self-end rounded-sm bg-[#ffa903] font-bold transition-colors duration-1000 hover:bg-[#5c2140] hover:text-white">
            Search
          </div>
        </div>
      </div>
    </>
  );
}

export default AddressOption;

interface VehicleItemProp {
  Icon: JSX.Element;
  label: string;
}
function VehicleItem({ Icon, label }: VehicleItemProp) {
  const textIcon = clsx(
    "ml-2",
    "text-white",
    "text-[14px]",
    "font-semibold",
    "flex",
    "items-center",
    "w-[50px]",
    "h-[30px]",
  );
  return (
    <>
      <div
        className={clsx(
          "w-[120px]",
          "h-[40px]",
          "flex",
          "justify-center",
          "items-center",
          "rounded-md",
          "transition duration-300 ease-in-out hover:bg-[#ffffff38]",
          "hover:rounded-md",
        )}
      >
        <div className={clsx("size-[24px]")}>{Icon}</div>
        <div className={textIcon}>{label}</div>
      </div>
    </>
  );
}
interface OptionItemProp extends VehicleItemProp {
  color?: string;
  bg?: string;
}
function OptionItem({
  Icon,
  label,
  color = "text-[#fff]",
  bg,
}: OptionItemProp) {
  return (
    <>
      <div
        className={clsx(
          "flex",
          "justify-center",
          "items-center",
          bg,
          "size-full",
        )}
      >
        <div>{Icon}</div>
        <div
          className={clsx(
            "ml-2 flex h-[30px] w-[50px] w-[100px] items-center text-[16px] font-semibold",
            color,
          )}
        >
          {label}
        </div>
      </div>
    </>
  );
}
