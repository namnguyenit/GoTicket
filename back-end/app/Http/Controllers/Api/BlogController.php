<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Services\BlogService;
use Illuminate\Http\Request;
 
class BlogController extends Controller
{
    protected $blogService;
 
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }
 
    /**
     * GET /api/blogs
     * Lấy các bài blog mới nhất cho public.
     * Nhận query param: ?limit=5
     */
    public function getLatest(Request $request)
    {
        return $this->blogService->getLatestBlogs($request);
    }
 
    /**
     * GET /api/blogs/{id}
     * Lấy chi tiết 1 bài blog (public)
     */
    public function show($id)
    {
        // Tái sử dụng service hiện có
        return $this->blogService->adminGetBlogById($id);
    }
 
    /**
     * GET /api/blogs/{id}/trips
     * Lấy danh sách trips gắn với blog (public)
     */
    public function trips($id)
    {
        return $this->blogService->listTrips($id);
    }
}
