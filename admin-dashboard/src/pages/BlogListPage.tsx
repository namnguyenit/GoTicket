// admin-dashboard/src/pages/BlogListPage.tsx
import React, { useState, useEffect, useCallback } from 'react';
import { PlusCircle, Edit, Trash2, FileText, Loader2, AlertCircle } from 'lucide-react';
import { getBlogs, deleteBlog } from '../services/api';
import type { Blog } from '../types/index'; // Import interface Blog
import BlogCreateModal from '../components/BlogCreateModal';
import BlogEditModal from '../components/BlogEditModal';

// Hàm lấy URL ảnh (giả định ảnh public trong storage)
const getImageUrl = (imagePath: string) => {
    if (!imagePath) return "https://via.placeholder.com/100x60?text=No+Image";
    // Giả định base URL của backend API
    const BASE_API_URL = 'http://127.0.0.1:8000'; 
    return imagePath.startsWith('http') ? imagePath : `${BASE_API_URL}/storage/${imagePath}`;
}

function BlogListPage() {
    const [blogs, setBlogs] = useState<Blog[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState('');
    
    const [isCreating, setIsCreating] = useState(false);
    const [editingBlogId, setEditingBlogId] = useState<number | null>(null);

    const fetchBlogs = useCallback(async () => {
        setIsLoading(true);
        setError('');
        try {
            const response = await getBlogs();
            // Chuẩn hóa dữ liệu trả về thành mảng Blog[]
            const raw = response?.data;
            let list: any[] = [];
            if (Array.isArray(raw)) list = raw;
            else if (Array.isArray(raw?.data?.data)) list = raw.data.data; // envelope+pagination
            else if (Array.isArray(raw?.data)) list = raw.data; // envelope (không phân trang)
            else if (Array.isArray(raw?.items)) list = raw.items; // fallback
            setBlogs(list as any); 
        } catch (err: any) {
            console.error("Lỗi khi tải Blogs:", err);
            setError(err.response?.data?.message || 'Không thể tải danh sách bài viết.');
            setBlogs([]);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchBlogs();
    }, [fetchBlogs]);

    const handleCloseModal = (didUpdate = false) => {
        setIsCreating(false);
        setEditingBlogId(null);
        if (didUpdate) {
            fetchBlogs(); // Tải lại danh sách nếu có cập nhật
        }
    }

    const handleDelete = async (id: number, title: string) => {
        if (window.confirm(`Bạn có chắc chắn muốn xóa bài viết "${title}"?`)) {
            try {
                await deleteBlog(id);
                fetchBlogs(); // Tải lại danh sách
            } catch (err: any) {
                console.error("Lỗi khi xóa Blog:", err);
                alert(err.response?.data?.message || 'Xóa bài viết thất bại.');
            }
        }
    }

    return (
        <div className="space-y-8">
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-3xl font-bold text-[#1A2B4B]">Quản lý Blog</h1>
                <button 
                    onClick={() => setIsCreating(true)} 
                    className="flex items-center space-x-2 rounded-lg bg-blue-600 px-4 py-3 text-white font-semibold transition hover:bg-blue-700 shadow-md"
                >
                    <PlusCircle className="h-5 w-5" />
                    <span>Thêm bài viết mới</span>
                </button>
            </div>
            
            <div className="rounded-xl bg-white p-6 shadow-lg border border-gray-200">
                {isLoading ? (
                    <div className="flex h-40 items-center justify-center text-gray-500">
                        <Loader2 className="h-6 w-6 animate-spin mr-2" />
                        <span>Đang tải dữ liệu...</span>
                    </div>
                ) : error ? (
                    <div className="flex h-40 flex-col items-center justify-center text-red-600">
                         <AlertCircle className="h-8 w-8 mb-2" />
                        <p className='font-semibold'>Lỗi: {error}</p>
                    </div>
                ) : !Array.isArray(blogs) || blogs.length === 0 ? (
                    <div className="text-center py-10 text-gray-500">
                        <FileText className="h-10 w-10 mx-auto mb-2" />
                        Không tìm thấy bài viết nào.
                    </div>
                ) : (
                    <table className="w-full table-auto border-collapse text-left">
                        <thead>
                            <tr className="border-b-2 border-gray-200 bg-gray-50/50 text-sm uppercase text-gray-700">
                                <th className="p-3 font-semibold rounded-tl-xl">Ảnh</th>
                                <th className="p-3 font-semibold">Tiêu đề</th>
                                <th className="p-3 font-semibold">Ngày tạo</th>
                                <th className="p-3 font-semibold text-center rounded-tr-xl">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {Array.isArray(blogs) && blogs.map((blog) => (
                                <tr key={blog.id} className="border-b border-gray-100 hover:bg-blue-50/50 transition">
                                    <td className="p-2">
                                        <img 
                                            src={getImageUrl(blog.image)} 
                                            alt={blog.title} 
                                            className="h-16 w-28 object-cover rounded-md border border-gray-200" 
                                        />
                                    </td>
                                    <td className="p-3 font-semibold text-gray-800">
                                        {blog.title}
                                    </td>
                                    <td className="p-3 text-sm text-gray-600">
                                        {new Date(blog.created_at).toLocaleDateString('vi-VN')}
                                    </td>
                                    <td className="p-3 text-center space-x-2">
                                        <button 
                                            title="Chỉnh sửa" 
                                            onClick={() => setEditingBlogId(blog.id)}
                                            className="text-blue-600 hover:text-blue-800 transition p-2 rounded-full hover:bg-blue-100"
                                        >
                                            <Edit className="h-5 w-5" />
                                        </button>
                                        <button 
                                            title="Xóa" 
                                            onClick={() => handleDelete(blog.id, blog.title)}
                                            className="text-red-600 hover:text-red-800 transition p-2 rounded-full hover:bg-red-100"
                                        >
                                            <Trash2 className="h-5 w-5" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>

            {/* Modals */}
            {isCreating && (
                <BlogCreateModal onClose={handleCloseModal} />
            )}
            {editingBlogId && (
                <BlogEditModal 
                    blogId={editingBlogId}
                    onClose={handleCloseModal}
                />
            )}
        </div>
    );
}

export default BlogListPage;