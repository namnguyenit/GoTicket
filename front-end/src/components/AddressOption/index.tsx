import clsx from "clsx";
import Assets from "../../assets";
import { useState, type JSX } from "react";
import Select from "../Select";

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
  const [from, setFrom] = useState<string | null>(null);
  return (
    <>
      <div className={clsx("w-[83vw]", "h-[50vh]", "grid", "grid-rows-6")}>
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
              "grid h-[50%] w-[100%] grid-cols-3 justify-items-center overflow-hidden rounded-t-2xl bg-[#622243]",
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
                  setFrom(e.city.name + " - " + e.location.name);
                }}
                title={from ? from : "From   ..."}
              />
            </div>
            <input
              className="pl-2.5 font-bold placeholder-[#622243] outline-1 outline-[#c2c2c2]"
              name="to"
              placeholder="To"
            />
            <input
              className="rounded-r-md pl-2.5 font-bold placeholder-[#622243] outline-1 outline-[#c2c2c2]"
              type="date"
              name="date"
            />
          </div>
          {/* SearchButton */}
          <div className="mr-[5%] flex h-12 w-42 items-center justify-center self-center justify-self-end rounded-md bg-[#ffa903] font-bold transition-colors duration-1000 hover:bg-[#5c2140] hover:text-white">
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
            "ml-2 flex h-[30px] w-[50px] w-[100px] items-center text-[18px] font-semibold",
            color,
          )}
        >
          {label}
        </div>
      </div>
    </>
  );
}
