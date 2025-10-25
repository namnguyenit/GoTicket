// admin-dashboard/src/types/index.ts

export interface Blog {
  id: number;
  title: string;
  content: string;
  image: string; // Đây sẽ là URL của ảnh
  created_at: string;
  updated_at: string;
  author_id?: number; // Thêm author_id dựa trên migration
  published_at?: string | null; // Thêm published_at dựa trên migration
}

// Định nghĩa cấu trúc response chung từ API (nếu chưa có)
export interface ApiListResponse<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

export interface ApiDetailResponse<T> {
    data: T;
}