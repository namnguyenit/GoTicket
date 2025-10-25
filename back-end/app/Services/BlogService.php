<?php

namespace App\Services;

use App\Repositories\Admin\BlogRepositoryInterface;
use App\Http\Helpers\ResponseHelper; // <- Giữ nguyên
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Enums\ApiSuccess; // <-- Thêm dòng này
use App\Enums\ApiError;   // <-- Thêm dòng này

class BlogService
{
    use ResponseHelper; // <-- THÊM DÒNG NÀY ĐỂ SỬ DỤNG TRAIT

    protected $blogRepository;

    public function __construct(BlogRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    // ... (các hàm khác) ...

    /**
     * Lấy các bài blog mới nhất cho người dùng (public)
     */
    public function getLatestBlogs(Request $request)
    {
        $limit = $request->input('limit', 5);
        $blogs = $this->blogRepository->getLatest($limit);
        
        // SỬA CÁCH GỌI: dùng $this-> và đổi tham số
        return $this->success($blogs, ApiSuccess::ACTION_SUCCESS);
    }

    /**
     * Lấy tất cả bài blog cho admin (phân trang)
     */
    public function adminGetAllBlogs(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $blogs = $this->blogRepository->adminGetAll($perPage);
        
        // SỬA CÁCH GỌI:
        return $this->success($blogs, ApiSuccess::ACTION_SUCCESS);
    }

    /**
     * Lấy 1 bài blog theo ID
     */
    public function adminGetBlogById($id)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
            // SỬA CÁCH GỌI:
            return $this->error(ApiError::NOT_FOUND);
        }
        // SỬA CÁCH GỌI:
        return $this->success($blog, ApiSuccess::ACTION_SUCCESS);
    }

    /**
     * Tạo bài blog mới
     */
    public function createBlog($request)
    {
        $data = $request->validated();
        // $data['user_id'] = Auth::id(); // Đã vô hiệu hóa ở lần trước

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = $path;
        }

        $blog = $this->blogRepository->create($data);
        // SỬA CÁCH GỌI:
        return $this->success($blog, ApiSuccess::CREATED_SUCCESS); // Dùng CREATED (201)
    }

    /**
     * Cập nhật bài blog
     */
    public function updateBlog($request, $id)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
             // SỬA CÁCH GỌI:
            return $this->error(ApiError::NOT_FOUND);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = $path;
        }

        $blog = $this->blogRepository->update($id, $data);
         // SỬA CÁCH GỌI:
        return $this->success($blog, ApiSuccess::ACTION_SUCCESS);
    }

    /**
     * Xóa bài blog
     */
    public function deleteBlog($id)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
            // SỬA CÁCH GỌI:
            return $this->error(ApiError::NOT_FOUND);
        }

        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $this->blogRepository->delete($id);

        // SỬA CÁCH GỌI:
        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}