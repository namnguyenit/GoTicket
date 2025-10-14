import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  User,
  MapPin,
  CreditCard,
  Ticket,
  ArrowRight,
  Clock,
  Bus,
} from "lucide-react";

const passengerFormFields: { id: string; label: string; type: string }[] = [
  { id: "fullName", label: "Họ và tên", type: "text" },
  { id: "phone", label: "Số điện thoại", type: "tel" },
  { id: "email", label: "Email", type: "email" },
];

const paymentMethods: {
  id: string;
  icon: "credit-card" | "wallet" | "qr-code";
  title: string;
  description: string;
}[] = [
  {
    id: "credit-card",
    icon: "credit-card",
    title: "Thẻ tín dụng/ghi nợ",
    description: "Thanh toán bằng thẻ nội địa hoặc quốc tế",
  },
  {
    id: "wallet",
    icon: "wallet",
    title: "Ví điện tử",
    description: "Thanh toán qua Momo, ZaloPay...",
  },
  {
    id: "vnpay",
    icon: "qr-code",
    title: "VNPay QR",
    description: "Quét mã QR để thanh toán",
  },
];

function CheckOut() {
  return (
    <>
      <div className="min-h-screen w-full bg-gradient-to-b from-[#faf6f6] via-[#f7f1f1] to-[#f3eded]">
        {/* Hero */}
        <div className="relative h-[260px] w-full bg-[url(/book-page-bg.jpg)] bg-cover bg-center">
          <div className="absolute inset-0 bg-black/20" />
          <div className="absolute inset-0 flex items-center justify-center">
            <div className="text-4xl font-bold text-white drop-shadow-[0_2px_6px_rgba(0,0,0,0.35)] md:text-6xl">
              Thanh Toán
            </div>
          </div>
        </div>
        {/* Content */}
        <div className="mx-auto w-full max-w-[1180px] px-4 py-8 md:px-6 md:py-10 lg:px-8">
          <div className="grid grid-cols-1 gap-5 md:gap-7 lg:grid-cols-[1fr_400px] lg:gap-8">
            {/* Left column */}
            <div className="flex flex-col gap-8">
              <Card className="overflow-hidden rounded-xl border border-[#ffffff1a] bg-white/40 shadow-[0_4px_14px_rgba(0,0,0,0.05)] backdrop-blur-sm">
                <div className="flex h-14 items-center gap-3 bg-[#5b2642] px-5 md:h-16 md:gap-4 md:px-7">
                  <User
                    className="h-7 w-7 text-white md:h-9 md:w-9"
                    strokeWidth={1.5}
                  />
                  <h2 className="[font-family:'Inter-Bold',Helvetica] text-xl leading-[normal] font-bold tracking-[0] whitespace-nowrap text-white md:text-2xl">
                    Thông tin hành khách
                  </h2>
                </div>
                <CardContent className="p-5 md:p-6">
                  <div className="flex flex-col gap-5">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                      {passengerFormFields.map((field) => (
                        <div key={field.id} className="flex flex-col gap-2.5">
                          <Label
                            htmlFor={field.id}
                            className="[font-family:'Inter-SemiBold',Helvetica] text-xs leading-[normal] font-semibold tracking-[0] text-[#5b2642] md:text-sm"
                          >
                            {field.label}
                          </Label>
                          <Input
                            id={field.id}
                            type={field.type}
                            className="h-10 rounded-[10px] border border-[#dcdcdc] bg-white/70 focus-visible:ring-2 focus-visible:ring-[#F7AC3D] md:h-11"
                          />
                        </div>
                      ))}
                    </div>
                    <div className="flex flex-col gap-3">
                      <Label
                        htmlFor="pickup-point"
                        className="[font-family:'Inter-SemiBold',Helvetica] text-sm leading-[normal] font-semibold tracking-[0] text-[#5b2642] md:text-base"
                      >
                        Chọn điểm đón *
                      </Label>
                      <div className="relative">
                        <Select>
                          <SelectTrigger
                            id="pickup-point"
                            className="h-10 rounded-[10px] border border-[#dcdcdc] bg-white/70 pl-[48px] focus-visible:ring-2 focus-visible:ring-[#F7AC3D] md:h-11"
                          >
                            <SelectValue
                              placeholder="Chọn từ danh sách điểm đón có sẵn"
                              className="[font-family:'Inter-SemiBold',Helvetica] text-sm font-semibold text-[#00000082] md:text-base"
                            />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="point1">Điểm đón 1</SelectItem>
                            <SelectItem value="point2">Điểm đón 2</SelectItem>
                          </SelectContent>
                        </Select>
                        <MapPin className="pointer-events-none absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-[#5b2642] md:h-5 md:w-5" />
                      </div>
                    </div>
                    <div className="flex flex-col gap-3">
                      <Label
                        htmlFor="dropoff-point"
                        className="[font-family:'Inter-SemiBold',Helvetica] text-sm leading-[normal] font-semibold tracking-[0] text-[#5b2642] md:text-base"
                      >
                        Chọn điểm trả *
                      </Label>
                      <div className="relative">
                        <Select>
                          <SelectTrigger
                            id="dropoff-point"
                            className="h-10 rounded-[10px] border border-[#dcdcdc] bg-white/70 pl-[48px] focus-visible:ring-2 focus-visible:ring-[#F7AC3D] md:h-11"
                          >
                            <SelectValue
                              placeholder="Chọn từ danh sách điểm trả có sẵn"
                              className="[font-family:'Inter-SemiBold',Helvetica] text-sm font-semibold text-[#00000082] md:text-base"
                            />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="point1">Điểm trả 1</SelectItem>
                            <SelectItem value="point2">Điểm trả 2</SelectItem>
                          </SelectContent>
                        </Select>
                        <MapPin className="pointer-events-none absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-[#5b2642] md:h-5 md:w-5" />
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card className="overflow-hidden rounded-xl border border-[#ffffff1a] bg-white/40 shadow-[0_4px_14px_rgba(0,0,0,0.05)] backdrop-blur-sm">
                <div className="flex h-14 items-center bg-[#5b2642] px-5 md:h-16 md:px-10">
                  <CreditCard className="mr-3 h-[26px] w-[26px] text-white md:h-7 md:w-7" />
                  <h2 className="[font-family:'Inter-Bold',Helvetica] text-xl leading-[normal] font-bold tracking-[0] whitespace-nowrap text-white md:text-2xl">
                    Chọn phương thức thanh toán
                  </h2>
                </div>
                <CardContent className="p-5 md:p-6">
                  <RadioGroup
                    defaultValue="credit-card"
                    className="flex flex-col gap-3 md:gap-4"
                  >
                    {paymentMethods.map((method) => (
                      <label
                        key={method.id}
                        htmlFor={method.id}
                        className="flex h-12 cursor-pointer items-center rounded-[10px] border border-[#dcdcdc] bg-white/70 px-3 transition-colors hover:border-[#c9c9c9] md:h-[54px]"
                      >
                        <RadioGroupItem
                          value={method.id}
                          id={method.id}
                          className="h-4 w-4 rounded-[10px]"
                        />
                        <div className="ml-3 flex items-center gap-4 md:gap-5">
                          {method.icon === "credit-card" && (
                            <CreditCard className="h-5 w-5 text-[#5b2642] md:h-6 md:w-6" />
                          )}
                          {method.icon === "wallet" && (
                            <div className="flex h-6 w-6 items-center justify-center md:h-[26px] md:w-[26px]">
                              <img
                                src=""
                                alt={method.title}
                                className="h-full w-full"
                              />
                            </div>
                          )}
                          {method.icon === "qr-code" && (
                            <div className="flex h-5 w-5 items-center justify-center md:h-6 md:w-6">
                              <img
                                src=""
                                alt={method.title}
                                className="h-full w-full"
                              />
                            </div>
                          )}
                          <div className="flex flex-col gap-[3px]">
                            <Label
                              htmlFor={method.id}
                              className="cursor-pointer [font-family:'Inter-SemiBold',Helvetica] text-sm leading-[normal] font-semibold tracking-[0] text-[#5b2642] md:text-base"
                            >
                              {method.title}
                            </Label>
                            <span className="[font-family:'Inter-SemiBold',Helvetica] text-[10px] leading-[normal] font-semibold tracking-[0] text-[#5b2642ad] md:text-xs">
                              {method.description}
                            </span>
                          </div>
                        </div>
                      </label>
                    ))}
                  </RadioGroup>
                </CardContent>
              </Card>
            </div>

            {/* Right column (summary) */}
            <Card className="h-max overflow-hidden rounded-xl border border-[#ffffff1a] bg-white/40 shadow-[0_4px_14px_rgba(0,0,0,0.05)] backdrop-blur-sm lg:sticky lg:top-6">
              <div className="flex h-14 items-center gap-3 bg-[#5b2642] px-5 md:h-16 md:gap-4 md:px-6">
                <Ticket
                  className="h-7 w-7 text-white md:h-9 md:w-9"
                  strokeWidth={1.5}
                />
                <h2 className="[font-family:'Inter-Bold',Helvetica] text-xl leading-[normal] font-bold tracking-[0] text-white md:text-2xl">
                  Tóm tắt chuyến đi
                </h2>
              </div>
              <CardContent className="p-5 md:p-6">
                <div className="flex flex-col gap-5 md:gap-6">
                  <div className="flex items-center gap-6 md:gap-7">
                    <MapPin className="h-6 w-5 text-[#5b2642] md:h-7 md:w-6" />
                    <div className="flex items-center gap-6 md:gap-7">
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#5b2642] md:text-base">
                        Hà Tĩnh
                      </span>
                      <ArrowRight className="h-5 w-5 text-[#5b2642] md:h-6 md:w-6" />
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#5b2642] md:text-base">
                        Hà Nội
                      </span>
                    </div>
                  </div>
                  <div className="flex items-center gap-3 md:gap-4">
                    <Clock className="h-5 w-5 text-[#5b2642] md:h-6 md:w-6" />
                    <div className="flex items-center gap-3 md:gap-4">
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#5b2642] md:text-base">
                        20:30 - 6:45
                      </span>
                      <div className="flex h-5 w-5 items-center justify-center md:h-6 md:w-6">
                        <div className="h-[2px] w-[2px] rounded-full bg-[#5b2642]" />
                      </div>
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#5b2642] md:text-base">
                        20/12/2025
                      </span>
                    </div>
                  </div>
                  <div className="flex items-center gap-2.5 md:gap-3">
                    <Bus className="h-5 w-5 text-[#5b2642] md:h-6 md:w-6" />
                    <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#5b2642] md:text-base">
                      Xe 01 - Nhà xe Văn Minh - Xe Thường
                    </span>
                  </div>
                  <div className="my-4 h-px bg-[#cccccc]" />
                  <div className="flex flex-col gap-3">
                    <h3 className="[font-family:'Inter-Medium',Helvetica] text-lg leading-[normal] font-medium tracking-[0] text-[#5b2642] md:text-xl">
                      Chi tiết chỗ ngồi
                    </h3>
                    <div className="flex h-[46px] items-center rounded-[10px] border border-[#0000002a] bg-[#f5f5f7] px-4 md:h-[52px] md:px-5">
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] text-[#5b2642] md:text-base">
                        Ghế 2A
                      </span>
                    </div>
                  </div>
                  <div className="my-4 h-px bg-[#cccccc]" />
                  <div className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#0000009e] md:text-base">
                        Giá vé:
                      </span>
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#0000009e] md:text-base">
                        300.000đ
                      </span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#0000009e] md:text-base">
                        Phụ phí:
                      </span>
                      <span className="[font-family:'Inter-Medium',Helvetica] text-sm leading-[normal] font-medium tracking-[0] whitespace-nowrap text-[#0000009e] md:text-base">
                        10.000đ
                      </span>
                    </div>
                  </div>
                  <div className="my-4 h-px bg-[#cccccc]" />
                  <div className="flex items-center justify-between">
                    <span className="[font-family:'Inter-SemiBold',Helvetica] text-lg leading-[normal] font-semibold tracking-[0] whitespace-nowrap text-[#5b2642] md:text-xl">
                      Tổng cộng
                    </span>
                    <span className="[font-family:'Inter-Bold',Helvetica] text-xl leading-[normal] font-bold tracking-[0] text-[#5b2642] md:text-2xl">
                      310.000đ
                    </span>
                  </div>
                  <Button className="mt-5 h-12 w-full rounded-[10px] bg-gradient-to-r from-[#f59e0b] to-[#f59e0b] hover:from-[#d97706] hover:to-[#d97706] md:mt-6 md:h-14">
                    <span className="[font-family:'Inter-Bold',Helvetica] text-base leading-[normal] font-bold tracking-[0] whitespace-nowrap text-white md:text-lg">
                      Xác nhận và Thanh toán
                    </span>
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </>
  );
}

export default CheckOut;
