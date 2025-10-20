import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { HelpCircle, LogOut, Pencil, Settings, User } from "lucide-react";
const navigationItems = [
  {
    id: "public-profile",
    label: "Public profile",
    icon: User,
    active: true,
  },
  {
    id: "setting",
    label: "Setting",
    icon: Settings,
    active: false,
  },
];
const supportItems = [
  {
    id: "help-support",
    label: "Help & Support",
    icon: HelpCircle,
  },
];
const logoutItems = [
  {
    id: "logout",
    label: "Đăng xuất",
    icon: LogOut,
    variant: "destructive",
  },
];
export default function Profile() {
  return (
    <div className="relative h-[130vh] w-full bg-gradient-to-b from-[#faf6f6] via-[#f7f1f1] to-[#f3eded]">
      <div className="absolute top-[80px] left-1/2 w-full max-w-6xl -translate-x-1/2 px-4 py-8 md:px-6 md:py-10 lg:px-8">
        {/* Header */}
        <header className="mb-6 flex items-center justify-between rounded-xl border border-white/50 bg-white/70 p-4 shadow-sm backdrop-blur-sm md:mb-8 md:p-6">
          <div className="flex items-center gap-4 md:gap-5">
            <Avatar className="h-12 w-12 rounded-full border-4 border-[#f7ac3d] bg-white md:h-14 md:w-14">
              <AvatarFallback className="bg-transparent">
                <User className="h-6 w-6 text-[#5b2642] md:h-7 md:w-7" />
              </AvatarFallback>
            </Avatar>
            <div className="flex flex-col">
              <span className="text-sm font-medium text-[#f7ac3d]">
                Your personal account
              </span>
              <h1 className="text-xl font-semibold text-[#5b2642] md:text-2xl">
                Peter Griffin
              </h1>
            </div>
          </div>
          <Button
            variant="outline"
            className="border-[#1b1f2426] bg-white px-3 py-1 shadow-[inset_0px_1px_0px_#ffffff40,0px_1px_0px_#1b1f240a]"
          >
            <span className="text-xs font-medium text-[#24292f] md:text-sm">
              Go to your personal profile
            </span>
          </Button>
        </header>

        {/* Main */}
        <div className="grid grid-cols-1 gap-6 md:grid-cols-[260px_1fr]">
          {/* Sidebar */}
          <aside className="flex h-max flex-col gap-4 rounded-xl border border-white/50 bg-white/70 p-4 shadow-sm backdrop-blur-sm">
            <nav className="flex flex-col gap-2">
              {navigationItems.map((item) => (
                <button
                  key={item.id}
                  className={`group relative flex items-center gap-2 rounded-md px-3 py-2 text-left transition-colors ${
                    item.active
                      ? "bg-[#5b2642] text-white"
                      : "text-[#5b2642] hover:bg-[#5b26420d]"
                  }`}
                >
                  <item.icon
                    className={`h-4 w-4 ${item.active ? "text-white" : "text-[#5b2642]"}`}
                  />
                  <span
                    className={`${item.active ? "font-semibold" : "font-normal"}`}
                  >
                    {item.label}
                  </span>
                  {item.active && (
                    <div className="absolute top-1/2 left-0 -translate-y-1/2 rounded-md bg-[#5b2642]" />
                  )}
                </button>
              ))}
            </nav>

            <Separator className="bg-[#d0d7de7a]" />

            <div>
              <div className="mb-2 text-xs font-semibold tracking-wide text-[#5b2642] uppercase">
                Support
              </div>
              {supportItems.map((item) => (
                <button
                  key={item.id}
                  className="flex items-center gap-2 rounded-md px-3 py-2 text-left text-[#5b2642] hover:bg-[#5b26420d]"
                >
                  <item.icon className="h-5 w-5" />
                  <span className="text-sm">{item.label}</span>
                </button>
              ))}
            </div>

            <Separator className="bg-[#d0d7de7a]" />

            <div>
              <div className="mb-2 text-xs font-semibold tracking-wide text-[#5b2642] uppercase">
                Đăng xuất
              </div>
              {logoutItems.map((item) => (
                <button
                  key={item.id}
                  className="flex items-center gap-2 rounded-md px-3 py-2 text-left text-[#dc0000] hover:bg-red-50"
                >
                  <item.icon className="h-5 w-5" />
                  <span className="text-sm">{item.label}</span>
                </button>
              ))}
            </div>

            {/* Profile picture card */}
            <div className="rounded-xl border border-white/50 bg-white/70 p-4 shadow-sm">
              <Label className="text-sm font-semibold text-[#5b2642]">
                Profile picture
              </Label>
              <div className="mt-3 flex flex-col items-center gap-3">
                <Avatar className="h-40 w-40 border-4 border-[#f7ac3d] bg-white">
                  <AvatarFallback className="bg-transparent">
                    <User className="h-20 w-20 text-[#5b2642]" />
                  </AvatarFallback>
                </Avatar>
                <Button
                  variant="outline"
                  className="h-8 border-[#d0d7de] bg-white px-3 py-0"
                >
                  <Pencil className="mr-1 h-4 w-4" />
                  <span className="text-sm">Edit</span>
                </Button>
              </div>
            </div>
          </aside>

          {/* Content */}
          <section className="rounded-xl border border-white/50 bg-white/80 p-5 shadow-sm backdrop-blur-sm md:p-6">
            <div className="mb-4 border-b border-[#d8dee4] pb-3">
              <h2 className="text-xl font-semibold text-[#5b2642] md:text-[23px]">
                Public profile
              </h2>
            </div>

            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Name
                </Label>
                <Input
                  defaultValue="Nguyễn Thành Nhân"
                  className="h-9 border-[#d0d7de] bg-white text-sm text-[#24292f] shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
                <p className="text-xs text-[#623d51]">
                  Your name may appear around GitHub where you contribute or are
                  mentioned. You can remove it at any time.
                </p>
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Email
                </Label>
                <Input
                  defaultValue="n@gmail.com"
                  className="h-9 border-[#d0d7de] bg-white text-sm text-black shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
                <p className="text-xs text-[#623d51]">
                  You have set your email address to private. To toggle email
                  privacy, go to{" "}
                  <span className="text-[#f7ac3d]">email settings</span> and
                  uncheck "Keep my email address private."
                </p>
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Mật khẩu hiện tại
                </Label>
                <Input
                  type="password"
                  defaultValue="............."
                  className="h-9 border-[#d0d7de] bg-white text-[32px] font-bold tracking-[0.32px] text-[#959595] shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Mật khẩu mới
                </Label>
                <Input
                  type="password"
                  defaultValue="............."
                  className="h-9 border-[#d0d7de] bg-white text-[32px] font-bold tracking-[0.32px] text-[#959595] shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Xác nhận mật khẩu mới
                </Label>
                <Input
                  type="password"
                  defaultValue="............."
                  className="h-9 border-[#d0d7de] bg-white text-[32px] font-bold tracking-[0.32px] text-[#959595] shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <Label className="text-sm font-semibold text-[#5b2642]">
                  Location
                </Label>
                <Input
                  defaultValue="Cẩm xuyên ,Hà Tĩnh"
                  className="h-9 border-[#d0d7de] bg-white text-sm text-[#24292f] shadow-[inset_0px_1px_0px_#d0d7de33]"
                />
              </div>

              <div className="col-span-1 flex flex-col gap-2">
                <div className="flex items-start gap-2">
                  <Checkbox className="mt-1 h-[13px] w-[13px] border-[#767676] bg-white" />
                  <div>
                    <Label className="text-sm font-semibold text-[#5b2642]">
                      Display current local time
                    </Label>
                    <p className="text-xs text-[#623d51]">
                      Other users will see the time difference from their local
                      time.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <p className="mt-4 text-xs text-[#57606a]">
              All of the fields on this page are optional and can be deleted at
              any time, and by filling them out, you're giving us consent to
              share this data wherever your user profile appears. Please see our{" "}
              <span className="text-[#0969da]">privacy statement</span> to learn
              more about how we use this information.
            </p>

            <div className="mt-5">
              <Button className="bg-[#f7ac3d] px-4 py-2 text-white shadow hover:bg-[#f7ac3d]/90">
                Update profile
              </Button>
            </div>
          </section>
        </div>
      </div>
    </div>
  );
}
