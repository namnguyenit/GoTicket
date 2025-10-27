<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TripService;
use App\Services\LocationService;
use App\Http\Requests\Api\SearchRequest; 
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Resources\TripResource; 
use App\Http\Resources\TripDetailResource; 


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

        $originId = null;
        $destinationId = null;
        if (!empty($validated['origin_location']) && !empty($validated['destination_location'])) {
            $origin = $this->locationService->findIdBYName($validated['origin_location']);
            $destination = $this->locationService->findIdBYName($validated['destination_location']);
            if (!$origin || !$destination) {
                return $this->error(ApiError::NOT_FOUND, ['message' => 'Điểm đi hoặc điểm đến không hợp lệ.']);
            }
            $originId = $origin->id;
            $destinationId = $destination->id;
        }

        $criteria = $validated;
        if ($originId && $destinationId) {
            $criteria['origin_id'] = $originId;
            $criteria['destination_id'] = $destinationId;
        }

        $trips = $this->tripService->searchTrips($criteria);
        $requestedPage = $request->query('page', 1);

        if ($requestedPage > $trips->lastPage() && $trips->total() > 0) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Trang bạn yêu cầu không tồn tại.']);
        }
        $trips->appends($request->query());

        $resource = TripResource::collection($trips);
        $data = [
            'data' => $resource,
            'links' => [
                'first' => $trips->url(1),
                'last'  => $trips->url($trips->lastPage()),
                'prev'  => $trips->previousPageUrl(),
                'next'  => $trips->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $trips->currentPage(),
                'last_page'    => $trips->lastPage(),
                'per_page'     => $trips->perPage(),
                'total'        => $trips->total(),
            ],
        ];
        return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);

    }

    public function getTripDetail(int $id)
    {
        $trip = $this->tripService->getTripById($id);

        if (!$trip) {
            return $this->error(ApiError::NOT_FOUND);
        }


        return $this->success(new TripDetailResource($trip), ApiSuccess::GET_DATA_SUCCESS);
    }


    public function getTripStops(int $id)
    {
        $trip = $this->tripService->getTripStops($id);

        if (!$trip) {
            return $this->error(ApiError::NOT_FOUND);
        }

        $stops = $trip->stops;

        if ($stops->isEmpty()) {
            $vr = $trip->vendorRoute;
            if ($vr) {
                $template = $vr->stopsTemplate()->with('stop')->get();
                $pickupPoints = $template->where('stop_type','pickup')->map->stop->filter();
                $dropoffPoints = $template->where('stop_type','dropoff')->map->stop->filter();
                if ($pickupPoints->isNotEmpty() || $dropoffPoints->isNotEmpty()) {
                    return $this->success([
                        'pickup_points' => \App\Http\Resources\StopResource::collection($pickupPoints->values()),
                        'dropoff_points' => \App\Http\Resources\StopResource::collection($dropoffPoints->values()),
                    ], ApiSuccess::GET_DATA_SUCCESS);
                }
                // fallback: all vendor stops if no template
                $vendorStops = $vr->vendor?->stops ?? collect();
                return $this->success([
                    'pickup_points' => \App\Http\Resources\StopResource::collection($vendorStops),
                    'dropoff_points' => \App\Http\Resources\StopResource::collection($vendorStops),
                ], ApiSuccess::GET_DATA_SUCCESS);
            }
        }

        $pickupPoints = $stops->where('pivot.stop_type', 'pickup')->values();
        $dropoffPoints = $stops->where('pivot.stop_type', 'dropoff')->values();

        return $this->success([
            'pickup_points' => \App\Http\Resources\StopResource::collection($pickupPoints),
            'dropoff_points' => \App\Http\Resources\StopResource::collection($dropoffPoints),
        ], ApiSuccess::GET_DATA_SUCCESS);
    }
}