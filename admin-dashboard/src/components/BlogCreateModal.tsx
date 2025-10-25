// admin-dashboard/src/components/BlogCreateModal.tsx
import React, { useState } from 'react';
import { X, Save, Loader2, Image as ImageIcon, Type, FileText } from 'lucide-react';
import { createBlog } from '../services/api';

interface BlogCreateModalProps {
    onClose: (didUpdate?: boolean) => void;
}

const BlogCreateModal: React.FC<BlogCreateModalProps> = ({ onClose }) => {
    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [image, setImage] = useState<File | null>(null);
    const [imagePreview, setImagePreview] = useState<string | null>(null);
    
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            setImage(file);
            setImagePreview(URL.createObjectURL(file));
        }
    };

    const handleSave = async (e: React.FormEvent) => {
        e.preventDefault();
        if (isSaving || !title || !content || !image) {
             if (!image) setError("Vui lòng chọn ảnh bìa.");
             if (!content) setError("Vui lòng nhập nội dung.");
             if (!title) setError("Vui lòng nhập tiêu đề.");
            return;
        }

        setIsSaving(true);
        setError(null);

        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('image', image);

        try {
            await createBlog(formData);
            onClose(true); // Đóng modal và báo hiệu cần tải lại list
        } catch (err: any) {
            console.error("Lỗi khi tạo Blog:", err.response?.data);
            const backendErrors = err.response?.data?.errors;
            let errorMessage = err.response?.data?.message || "Tạo bài viết thất bại.";
            
            if (backendErrors) {
                 errorMessage = Object.values(backendErrors).flat().join(' ');
            }
            setError(errorMessage);
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="w-full max-w-3xl rounded-xl bg-white shadow-2xl transition-all duration-300">
                <header className="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 className="text-xl font-bold text-[#1A2B4B]">
                        Tạo bài viết mới
                    </h3>
                    <button onClick={() => onClose()} className="p-2 rounded-full hover:bg-gray-200 text-gray-700">
                        <X className="h-6 w-6" />
                    </button>
                </header>
                
                <form onSubmit={handleSave}>
                    <div className="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        
                        {/* Title */}
                        <FormInput 
                            icon={Type} 
                            label="Tiêu đề bài viết" 
                            name="title"
                            value={title}
                            onChange={(e) => setTitle(e.target.value)}
                            required
                        />
                        
                        {/* Content */}
                        <FormTextArea
                            icon={FileText}
                            label="Nội dung"
                            name="content"
                            value={content}
                            onChange={(e) => setContent(e.target.value)}
                            rows={10}
                            required
                        />

                        {/* Image Upload */}
                        <div>
                            <label className="text-xs font-medium text-gray-500 flex items-center gap-1 mb-1">
                                <ImageIcon className="h-4 w-4" /> Ảnh bìa (Bắt buộc)
                            </label>
                            {imagePreview ? (
                                <div className="mb-2">
                                    <img src={imagePreview} alt="Xem trước" className="w-full max-h-60 object-contain rounded-lg border border-gray-300 p-1"/>
                                </div>
                            ) : (
                                <div className="w-full h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400">
                                    Chưa chọn ảnh
                                </div>
                            )}
                            <input
                                type="file"
                                name="image"
                                accept="image/png, image/jpeg, image/webp"
                                onChange={handleImageChange}
                                className="mt-2 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                required
                            />
                        </div>

                        {error && <p className="text-sm text-red-600 p-2 bg-red-50 rounded-lg">{error}</p>}
                    </div>
                    
                    {/* Footer */}
                    <div className="p-4 flex justify-end space-x-3 bg-gray-50 rounded-b-xl border-t">
                        <button 
                            type="button" 
                            onClick={() => onClose()}
                            className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition"
                            disabled={isSaving}
                        >
                            Hủy
                        </button>
                        <button 
                            type="submit" 
                            className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition flex items-center gap-2 disabled:bg-gray-400"
                            disabled={isSaving}
                        >
                            {isSaving ? <Loader2 className="h-4 w-4 animate-spin" /> : <Save className="h-4 w-4" />}
                            {isSaving ? 'Đang lưu...' : 'Lưu bài viết'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

// Component helper (Bạn có thể tách ra file riêng nếu muốn)
const FormInput: React.FC<any> = ({ label, icon: Icon, fullWidth, ...props }) => (
    <div className={`flex flex-col gap-1 ${fullWidth ? "col-span-2" : "col-span-1"}`}>
        <label className="text-xs font-medium text-gray-500 flex items-center gap-1">
            <Icon className="h-4 w-4" /> {label}
        </label>
        <input
            type="text"
            {...props}
            className="rounded-lg border border-gray-300 p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
        />
    </div>
);

const FormTextArea: React.FC<any> = ({ label, icon: Icon, ...props }) => (
    <div className="flex flex-col gap-1 col-span-2">
        <label className="text-xs font-medium text-gray-500 flex items-center gap-1">
            <Icon className="h-4 w-4" /> {label}
        </label>
        <textarea
            {...props}
            className="rounded-lg border border-gray-300 p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
        />
    </div>
);

export default BlogCreateModal;