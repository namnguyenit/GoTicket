<?php
 
namespace App\Services;
 
use App\Repositories\Admin\BlogRepositoryInterface;
use App\Http\Helpers\ResponseHelper; // <- Giữ nguyên
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Enums\ApiSuccess; // <-- Thêm dòng này
use App\Enums\ApiError;   // <-- Thêm dòng này
use App\Http\Resources\TripResource;
 
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
 
        $hasImageColumn = Schema::hasColumn('blogs', 'image');
        if (!$hasImageColumn && array_key_exists('image', $data)) {
            unset($data['image']);
        }
        if ($hasImageColumn && $request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = $path;
        }
 
        // Chỉ giữ các trường hợp lệ
        $allowedKeys = $hasImageColumn ? ['title','content','image'] : ['title','content'];
        $data = array_intersect_key($data, array_flip($allowedKeys));
 
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
            if (Schema::hasColumn('blogs', 'image')) {
                if ($blog->image) {
                    Storage::disk('public')->delete($blog->image);
                }
                $path = $request->file('image')->store('blogs', 'public');
                $data['image'] = $path;
            }
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
            return $this->error(ApiError::NOT_FOUND);
        }
 
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
 
        $this->blogRepository->delete($id);
 
        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
 
    // Danh sách trips đã gắn với blog
    public function listTrips($id)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
            return $this->error(ApiError::NOT_FOUND);
        }
        $trips = $blog->trips()
            ->with(['vendorRoute.route.origin','vendorRoute.route.destination','coaches.vehicle'])
            ->latest('trips.id')
            ->get();
        return $this->success(TripResource::collection($trips), ApiSuccess::ACTION_SUCCESS);
    }
 
    // Gắn (attach hoặc sync) các trip vào blog
    public function attachTrips($id, array $tripIds, bool $sync = false)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
            return $this->error(ApiError::NOT_FOUND);
        }
        if ($sync) {
            $blog->trips()->sync($tripIds);
        } else {
            $blog->trips()->syncWithoutDetaching($tripIds);
        }
        return $this->success($blog->trips()->pluck('trips.id'), ApiSuccess::ACTION_SUCCESS);
    }
 
    // Bỏ gắn một trip khỏi blog
    public function detachTrip($id, $tripId)
    {
        $blog = $this->blogRepository->findById($id);
        if (!$blog) {
            return $this->error(ApiError::NOT_FOUND);
        }
        $blog->trips()->detach($tripId);
        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}
 
