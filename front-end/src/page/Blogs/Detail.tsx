import React, { useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import styles from './BlogDetail.module.css';
import { useFetch } from '@/hooks/useFetch';
import { URL } from '@/config';

type Blog = {
  id: number;
  title: string;
  content: string;
  image?: string | null;
  image_url?: string | null;
  created_at?: string;
};

type Envelope<T> = {
  success: boolean;
  status: number;
  message: string;
  data: T;
};

// TripResource shape per API_SPEC
interface TripItem {
  id: number;
  trip?: string | null; // e.g. "Hà Nội - Sapa"
  imageLink?: string | null;
  vendorName?: string | null;
  vendorType?: string | null;
  price?: number | null;
  departureDate?: string | null;
}

const API = `${URL}/api`;

function joinImage(base: string, path?: string | null): string | undefined {
  if (!path) return undefined;
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  if (!path.startsWith('/')) return `${base}/storage/${path}`; // raw storage path from DB
  return `${base}${path}`; // e.g. "/storage/..."
}

function formatVND(value?: number | null): string {
  if (typeof value !== 'number') return '';
  return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
}

function formatDate(iso?: string | null): string {
  if (!iso) return '';
  try {
    const d = new Date(iso);
    return d.toLocaleDateString('vi-VN');
  } catch {
    return '';
  }
}

const BlogDetail: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const blogId = Number(id);

  const blogFetch = useFetch<Envelope<Blog>>(API);
  const tripsFetch = useFetch<Envelope<TripItem[]>>(API);

  useEffect(() => {
    if (!blogId) return;
    blogFetch.get(`/blogs/${blogId}`);
    tripsFetch.get(`/blogs/${blogId}/trips`);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [blogId]);

  const blog = (blogFetch.data?.data as Blog) || undefined;
  const trips = (tripsFetch.data?.data as TripItem[]) || [];

  const coverUrl = useMemo(() => {
    return (
      joinImage(URL, blog?.image_url) ||
      joinImage(URL, blog?.image) ||
      `${URL}/welcome.jpg`
    );
  }, [blog]);

  return (
    <div className={styles.pageWrap}>
      <section className={styles.cover} style={{ backgroundImage: `url(${coverUrl})` }}>
        <div className={styles.overlay} />
        <div className={styles.coverInner}>
          <h1 className={styles.title}>{blog?.title || 'Bài viết'}</h1>
        </div>
      </section>

      <div className={styles.container}>
        <main className={styles.main}>
          <div className={styles.metaRow}>
            <div className={styles.metaItem}>
              <span className={styles.metaIcon}>📝</span>
              <span>GoTicket Blog</span>
            </div>
            <div className={styles.metaItem}>
              <span className={styles.metaIcon}>📅</span>
              <span>{formatDate(blog?.created_at)}</span>
            </div>
          </div>

          {blogFetch.loading ? (
            <div className={styles.loading}>Đang tải bài viết...</div>
          ) : blogFetch.error ? (
            <div className={styles.error}>Lỗi: {blogFetch.error}</div>
          ) : (
            <article className={styles.article}>
              {blog?.content ? (
                <div className={styles.content}>
                  {blog.content}
                </div>
              ) : (
                <div className={styles.contentEmpty}>Không có nội dung.</div>
              )}

              <div className={styles.shareRow}>
                <span>Chia sẻ bài viết</span>
                <button
                  className={styles.copyBtn}
                  onClick={() => {
                    const link = window.location.href;
                    navigator.clipboard.writeText(link);
                  }}
                >
                  Sao chép liên kết
                </button>
              </div>
            </article>
          )}
        </main>

        <aside className={styles.sidebar}>
          <div className={styles.sidebarCard}>
            <div className={styles.sidebarHeader}>
              <span className={styles.sidebarIcon}>🚌</span>
              <div>
                <h3 className={styles.sidebarTitle}>Chuyến đi liên quan</h3>
                <p className={styles.sidebarSub}>Đặt vé ngay để khám phá điểm đến trong bài viết</p>
              </div>
            </div>

            {tripsFetch.loading && <div className={styles.loadingSmall}>Đang tải chuyến đi...</div>}
            {tripsFetch.error && <div className={styles.errorSmall}>Lỗi: {tripsFetch.error}</div>}

            <div className={styles.tripList}>
              {trips?.map((t) => (
                <div key={t.id} className={styles.tripItem}>
                  <div className={styles.tripInfo}>
                    <div className={styles.tripRoute}>{t.trip || 'Chuyến đi'}</div>
                    {t.vendorName && <div className={styles.tripVendor}>{t.vendorName}</div>}
                  </div>
                  <div className={styles.tripPrice}>{formatVND(t.price)}</div>
                  <Link to={`/book`} className={styles.bookBtn}>Đặt vé ngay</Link>
                </div>
              ))}
              {!tripsFetch.loading && !tripsFetch.error && trips?.length === 0 && (
                <div className={styles.emptyTrips}>Chưa có chuyến đi liên quan.</div>
              )}
            </div>
          </div>
        </aside>
      </div>
    </div>
  );
};

export default BlogDetail;
