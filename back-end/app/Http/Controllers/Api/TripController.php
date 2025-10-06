<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TripService;
use App\Services\LocationService;
use App\Http\Requests\Api\SearchRequest; // Dùng lại SearchRequest bạn đã tạo
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Resources\TripResource; 

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
        // SearchRequest đã tự động lấy tất cả tham số từ URL và validate chúng
        $validated = $request->validated();

        $origin = $this->locationService->findIdBYName($validated['origin_location']);
        $destination = $this->locationService->findIdBYName($validated['destination_location']);

        if (!$origin || !$destination) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Điểm đi hoặc điểm đến không hợp lệ.']);
        }
        
        // Gói tất cả các điều kiện, bao gồm cả những cái có thể là null
        $criteria = array_merge($validated, [
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
        ]);

        $trips = $this->tripService->searchTrips($criteria);
        $requestedPage = $request->query('page', 1);

        // Nếu người dùng yêu cầu một trang lớn hơn trang cuối cùng VÀ tổng số kết quả > 0
        if ($requestedPage > $trips->lastPage() && $trips->total() > 0) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Trang bạn yêu cầu không tồn tại.']);
        }
        $trips->appends($request->query());
        // THAY ĐỔI Ở ĐÂY: Bọc kết quả trong Resource
        // Điều này sẽ không thay đổi cấu trúc phân trang
        return $this->success($trips, ApiSuccess::GET_DATA_SUCCESS);

    }
}