import React, { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { URL } from "@/config";
import { useFetch } from "@/hooks/useFetch";
import {
  Calendar,
  User2,
  Search,
  ChevronRight,
  Tag as TagIcon,
} from "lucide-react";

type BlogPost = {
  id: number | string;
  title: string;
  content?: string;
  image?: string | null;
  image_url?: string | null;
  created_at?: string;
};

type ApiResponse<T> = {
  success: boolean;
  status: number;
  message: string;
  data: T;
};

function formatDate(date?: string) {
  if (!date) return "";
  try {
    return new Date(date).toLocaleDateString("vi-VN", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  } catch {
    return date;
  }
}

function excerpt(html?: string, max = 140) {
  if (!html) return "";
  const text = html
    .replace(/<[^>]*>/g, " ")
    .replace(/\s+/g, " ")
    .trim();
  if (text.length <= max) return text;
  return text.slice(0, max).trimEnd() + "…";
}

const CATEGORIES = [
  { key: "diem-den", label: "Điểm đến" },
  { key: "kinh-nghiem", label: "Kinh nghiệm" },
  { key: "am-thuc", label: "Ẩm thực" },
  { key: "van-hoa", label: "Văn hóa" },
  { key: "phong-canh", label: "Phong cảnh" },
  { key: "nhiep-anh", label: "Nhiếp ảnh" },
];

const Blogs: React.FC = () => {
  const apiBase = `${URL}/api`;
  const { get, loading } = useFetch<ApiResponse<BlogPost[]>>(apiBase);
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [q, setQ] = useState("");

  useEffect(() => {
    (async () => {
      const res = (await get(`/blogs?limit=12`)) as ApiResponse<BlogPost[]> | undefined;
      if (res?.data) setPosts(res.data);
    })();
  }, [get]);

  const filtered = useMemo(() => {
    if (!q) return posts;
    const term = q.toLowerCase();
    return posts.filter(
      (b) =>
        b.title?.toLowerCase().includes(term) ||
        (b.content && b.content.toLowerCase().includes(term)),
    );
  }, [posts, q]);

  const featured = filtered[0];
  const rest = filtered.slice(1);

  return (
    <div className="min-h-screen bg-[#fbf2f2]">
      {/* Hero */}
      <section className="relative h-[360px] w-full overflow-hidden">
        <img
          src="/welcome.jpg"
          alt="Hero"
          className="absolute inset-0 h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-[#3b2667]/80 via-[#3b2667]/50 to-transparent" />
        <div className="relative mx-auto flex h-full max-w-7xl items-center px-4">
          <div>
            <h1 className="text-5xl md:text-6xl font-extrabold tracking-tight text-white drop-shadow-md">
              Cẩm nang du lịch
            </h1>
            <p className="mt-4 text-lg md:text-xl text-white/90">
              Khám phá Việt Nam qua từng hành trình
            </p>
          </div>
        </div>
      </section>

      {/* Main content */}
      <div className="mx-auto -mt-10 max-w-7xl px-4 pb-16">
        <div className="grid grid-cols-1 gap-6 md:grid-cols-4">
          {/* Left content */}
          <div className="md:col-span-3 space-y-6">
            {/* Featured card */}
            {featured && (
              <article className="rounded-xl border border-black/10 bg-white shadow-sm">
                <div className="flex flex-col gap-0 md:flex-row">
                  <div className="relative md:w-[46%]">
                    <img
                      src={featured.image_url || featured.image || "/HomePage/deal-1.jpg"}
                      alt={featured.title}
                      className="h-64 w-full rounded-t-xl object-cover md:h-full md:rounded-l-xl md:rounded-tr-none"
                    />
                    <span className="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-[#5c2ea1] px-3 py-1 text-xs font-medium text-white shadow">
                      <TagIcon size={14} /> Điểm đến
                    </span>
                  </div>
                  <div className="flex flex-1 flex-col gap-4 p-6">
                    <h2 className="text-2xl font-bold text-[#4b2c77]">
                      {featured.title}
                    </h2>
                    <p className="text-sm text-gray-600 leading-relaxed">
                      {excerpt(featured.content, 220)}
                    </p>
                      <div className="mt-auto flex items-center justify-between">
                        <div className="flex items-center gap-4 text-sm text-gray-600">
                          <span className="inline-flex items-center gap-1"><User2 size={16} /> GoTicket</span>
                          <span className="inline-flex items-center gap-1"><Calendar size={16} /> {formatDate(featured.created_at)}</span>
                        </div>
                        <Link to={`/blogs/${featured.id}`} className="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                          Đọc thêm <ChevronRight size={16} />
                        </Link>
                      </div>
                  </div>
                </div>
              </article>
            )}

            {/* Grid of posts */}
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
              {rest.map((b) => (
                <article
                  key={b.id}
                  className="rounded-xl border border-black/10 bg-white shadow-sm hover:shadow-md transition-shadow"
                >
                  <img
                    src={b.image_url || b.image || "/HomePage/deal-2.jpg"}
                    alt={b.title}
                    className="h-44 w-full rounded-t-xl object-cover"
                  />
                  <div className="p-5">
                    <h3 className="text-base font-semibold text-gray-900">
                      {b.title}
                    </h3>
                    <p className="mt-2 text-sm text-gray-600">
                      {excerpt(b.content, 140)}
                    </p>
                    <div className="mt-4 flex items-center justify-between text-sm text-gray-600">
                      <span className="inline-flex items-center gap-1"><User2 size={16} /> GoTicket</span>
                      <span className="inline-flex items-center gap-1"><Calendar size={16} /> {formatDate(b.created_at)}</span>
                    </div>
                    <Link to={`/blogs/${b.id}`} className="mt-3 inline-flex items-center gap-2 text-sm font-medium text-[#ff7a00] hover:underline">
                      Đọc thêm <ChevronRight size={16} />
                    </Link>
                  </div>
                </article>
              ))}

              {loading && Array.from({ length: 4 }).map((_, i) => (
                <div key={i} className="animate-pulse rounded-xl border border-black/10 bg-white p-5">
                  <div className="h-40 w-full rounded-lg bg-gray-200" />
                  <div className="mt-4 h-4 w-3/4 rounded bg-gray-200" />
                  <div className="mt-2 h-4 w-1/2 rounded bg-gray-200" />
                </div>
              ))}
            </div>
          </div>

          {/* Sidebar */}
          <aside className="md:col-span-1 space-y-6">
            {/* Search */}
            <div className="rounded-xl border border-black/10 bg-white p-4 shadow-sm">
              <h4 className="text-sm font-semibold text-gray-800">Tìm kiếm bài viết</h4>
              <div className="mt-3 flex items-center gap-2 rounded-lg border border-black/10 bg-gray-50 px-3 py-2">
                <Search size={18} className="text-gray-500" />
                <input
                  value={q}
                  onChange={(e) => setQ(e.target.value)}
                  placeholder="Nhập từ khóa..."
                  className="w-full bg-transparent text-sm outline-none placeholder:text-gray-500"
                />
              </div>
            </div>

            {/* Categories */}
            <div className="rounded-xl border border-black/10 bg-white p-4 shadow-sm">
              <h4 className="text-sm font-semibold text-gray-800">Danh mục</h4>
              <ul className="mt-2 space-y-2">
                {CATEGORIES.map((c) => (
                  <li key={c.key} className="flex items-center justify-between rounded-lg px-2 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <span className="inline-flex items-center gap-2"><TagIcon size={16} className="text-[#5c2ea1]" /> {c.label}</span>
                    <span className="h-2 w-2 rounded-full bg-gray-300" />
                  </li>
                ))}
              </ul>
            </div>

            {/* Recent posts */}
            <div className="rounded-xl border border-black/10 bg-white p-4 shadow-sm">
              <h4 className="text-sm font-semibold text-gray-800">Bài viết gần đây</h4>
              <div className="mt-3 space-y-3">
                {(posts.slice(0, 4)).map((p) => (
                  <div key={p.id} className="flex items-center gap-3">
                    <img
                      src={p.image_url || p.image || "/HomePage/deal-3.jpg"}
                      alt={p.title}
                      className="h-12 w-12 rounded object-cover"
                    />
                    <div className="min-w-0">
                      <p className="truncate text-sm font-medium text-gray-800">{p.title}</p>
                      <p className="text-xs text-gray-500">{formatDate(p.created_at)}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  );
};

export default Blogs;
