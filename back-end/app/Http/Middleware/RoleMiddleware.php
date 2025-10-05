<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// Import các file cần thiết
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;

class RoleMiddleware
{
    // Sử dụng "hộp dụng cụ" ResponseHelper
    use ResponseHelper;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles Các role được phép truy cập (vd: 'admin', 'vendor')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Lấy thông tin người dùng đã được xác thực trước đó
        $user = auth('api')->user();

        // Kiểm tra xem người dùng có tồn tại và role của họ có nằm trong danh sách
        // các role được phép truy cập hay không
        if (! $user || ! in_array($user->role, $roles)) {
            // Nếu không có quyền, trả về lỗi FORBIDDEN
            return $this->error(ApiError::FORBIDDEN);
        }

        // Nếu có quyền, cho phép request đi tiếp vào Controller
        return $next($request);
    }
}