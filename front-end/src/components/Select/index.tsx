import { useState } from "react";
import Assets from "../../assets";
import clsx from "clsx";
import style from "./Select.module.css";

interface SelectType {
  Item: {
    name: string;
    value: string;
    location: { name: string; value: string }[];
  }[];
  onChange: (value: any) => void;
  title?: string;
}

function Select({ Item, onChange, title = "Select . . ." }: SelectType) {
  const [focus, setFocus] = useState<boolean | null>(null);
  return (
    <>
      <div className="relative h-full w-full">
        <div
          className="relative flex h-full w-full items-center justify-center font-bold text-[#622243]"
          onClick={() => {
            setFocus(!focus);
          }}
        >
          {title}
          <div
            className={clsx(
              "absolute top-1/2 right-2 -translate-1/2 transition-transform",
              !focus && "-rotate-180",
            )}
          >
            <Assets.DropDown />
          </div>
        </div>

        <div
          className={clsx(
            "absolute top-[calc(100%+10px)] z-10 inline-grid w-full grid-cols-1 justify-center overflow-y-scroll rounded-md shadow-xl outline outline-[#6222437a] transition-[max-height] duration-700",
            focus !== null
              ? focus
                ? style.dropdown
                : style.close
              : style.hidden,
          )}
          style={{
            scrollbarColor: "#ffa903 #f1f1f1",
            scrollbarWidth: "thin",
          }}
        >
          {Item.map((item, index) => (
            <City Item={item} key={index} onChange={onChange} />
          ))}
        </div>
      </div>
    </>
  );
}

export default Select;

interface CityType {
  Item: {
    name: string;
    value: string;
    location: { name: string; value: string }[];
  };
  onChange: (value: any) => void;
}

function City({ Item, onChange }: CityType) {
  const [focus, setFocus] = useState(false);
  return (
    <>
      <div>
        <div
          className="relative flex h-8 w-full items-center justify-center bg-[#d6cfc1]"
          onClick={() => {
            setFocus(!focus);
          }}
        >
          {Item.name}
          <div
            className={clsx(
              "absolute top-1/2 right-2 -translate-1/2 transition-transform",
              !focus && "-rotate-180",
            )}
          >
            <Assets.DropDownCity />
          </div>
        </div>
        {focus && (
          <div className="grid w-full auto-rows-[32px] bg-[#f8f3e7]">
            {Item.location.map((item, index) => (
              <div
                key={index}
                className="grid h-8 w-full grid-cols-[90%] items-center justify-center hover:bg-[#ffa903]"
                onClick={() => {
                  onChange({
                    city: { name: Item.name, value: Item.value },
                    location: { name: item.name, value: item.value },
                  });
                }}
              >
                {item.name}
              </div>
            ))}
          </div>
        )}
      </div>
    </>
  );
}
