<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use App\Http\Requests\Api\Admin\CreateBlogRequest;
use App\Http\Requests\Api\Admin\UpdateBlogRequest;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    // GET /api/admin/blogs (Get all)
    public function index(Request $request)
    {
        return $this->blogService->adminGetAllBlogs($request);
    }

    // GET /api/admin/blogs/{id} (Get one)
    public function show($id)
    {
        return $this->blogService->adminGetBlogById($id);
    }

    // POST /api/admin/blogs (Create)
    public function store(CreateBlogRequest $request)
    {
        return $this->blogService->createBlog($request);
    }

    // POST /api/admin/blogs/{id} (Update)
    // Dùng POST để dễ dàng xử lý file upload (image)
    public function update(UpdateBlogRequest $request, $id)
    {
        return $this->blogService->updateBlog($request, $id);
    }

    // DELETE /api/admin/blogs/{id} (Delete)
    public function destroy($id)
    {
        return $this->blogService->deleteBlog($id);
    }
}