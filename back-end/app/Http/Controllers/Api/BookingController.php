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

    
    public function initiate(InitiateBookingRequest $request)
    {

        $user = auth('api')->user();

        $validated = $request->validated();
        
        try {

            $checkoutData = $this->bookingService->initiateBooking(
                $validated['trip_id'],
                $validated['seat_ids'],
                $user
            );

            return $this->success($checkoutData, ApiSuccess::GET_DATA_SUCCESS);

        } catch (ValidationException $e) {

            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
    
    public function confirm(ConfirmBookingRequest $request)
    {
        $user = auth('api')->user();
        $validated = $request->validated(); // Lấy tất cả dữ liệu đã được validate

        try {

            $booking = $this->bookingService->confirmBooking($validated, $user);

            return $this->success([
                'booking_code' => $booking->booking_code,
                'message' => 'Đặt vé thành công!',
            ], ApiSuccess::CREATED_SUCCESS);

        } catch (ValidationException $e) {
            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
}