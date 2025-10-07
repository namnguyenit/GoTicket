<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\Api\ConfirmBookingRequest;
use App\Http\Requests\Api\InitiateBookingRequest; // ✅ Import request mới
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    use ResponseHelper;

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * ✅ THÊM PHƯƠNG THỨC NÀY VÀO
     *
     * Khởi tạo phiên đặt vé, kiểm tra ghế và lấy thông tin cho trang thanh toán.
     */
    public function initiate(InitiateBookingRequest $request)
    {
        // Lấy người dùng đã đăng nhập từ token
        $user = auth('api')->user();
        
        // Lấy dữ liệu đã được validate bởi InitiateBookingRequest
        $validated = $request->validated();
        
        try {
            // Gọi service để xử lý nghiệp vụ
            $checkoutData = $this->bookingService->initiateBooking(
                $validated['trip_id'],
                $validated['seat_ids'],
                $user
            );

            // Trả về dữ liệu thành công cho frontend để hiển thị trang thanh toán
            return $this->success($checkoutData, ApiSuccess::GET_DATA_SUCCESS);

        } catch (ValidationException $e) {
            // Bắt lỗi nghiệp vụ từ Service (ví dụ: ghế đã được đặt) và trả về cho client
            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
    /**
     * Xác nhận và hoàn tất việc đặt vé.
     */
    public function confirm(ConfirmBookingRequest $request)
    {
        $user = auth('api')->user();
        $validated = $request->validated(); // Lấy tất cả dữ liệu đã được validate

        try {
            // ✅ SỬA LẠI DÒNG NÀY: CHỈ TRUYỀN 2 THAM SỐ
            $booking = $this->bookingService->confirmBooking($validated, $user);

            // ... (phần trả về response giữ nguyên)
            return $this->success([
                'booking_code' => $booking->booking_code,
                'message' => 'Đặt vé thành công!',
            ], ApiSuccess::CREATED_SUCCESS);

        } catch (ValidationException $e) {
            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
}