<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TripService;
use App\Services\LocationService;
use App\Http\Requests\Api\SearchRequest; // Dùng lại SearchRequest bạn đã tạo
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;
use App\Enums\ApiSuccess;

class TripController extends Controller
{
    use ResponseHelper;
    protected $tripService;
    protected $locationService;

    public function __construct(TripService $tripService, LocationService $locationService)
    {
        $this->tripService = $tripService;
        $this->locationService = $locationService;
    }

    public function search(SearchRequest $request)
    {
        $validated = $request->validated();

        // Lấy ID từ tên địa điểm
        $origin = $this->locationService->findIdBYName($validated['origin_location']);
        $destination = $this->locationService->findIdBYName($validated['destination_location']);

        if (!$origin || !$destination) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Điểm đi hoặc điểm đến không hợp lệ.']);
        }
        
        // Gói các điều kiện tìm kiếm vào một mảng
        $criteria = [
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'date' => $validated['date'],
            'vehicle_type' => $validated['vehicle_type'],
        ];

        $trips = $this->tripService->searchTrips($criteria);

        // Bạn có thể tạo một TripResource để định dạng dữ liệu trả về nếu muốn
        return $this->success($trips, ApiSuccess::GET_DATA_SUCCESS);
    }
}