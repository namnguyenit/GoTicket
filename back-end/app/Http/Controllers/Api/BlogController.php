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
}