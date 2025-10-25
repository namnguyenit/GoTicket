<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;
use App\Http\Requests\Api\Vendor\CreateTripRequest;
use App\Http\Requests\Api\Vendor\UpdateTripRequest;
use App\Http\Resources\Vendor\TripResource;
use App\Services\Vendor\TripService as VendorTripService;
use Illuminate\Http\Request;
use App\Models\Trips;

class TripController extends Controller
{
    use ResponseHelper;

    public function __construct(private VendorTripService $tripService)
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->query('per_page', 10);
            $vehicleType = $request->query('vehicle_type');
            $paginator = $this->tripService->listTripsByVendor($perPage, $vehicleType);
            $data = [
                'data' => TripResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ];
            return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function store(CreateTripRequest $request)
    {
        try {
            $trip = $this->tripService->createTrip($request->validated());
            return $this->success(new TripResource($trip), ApiSuccess::TRIP_CREATED);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function show(Trips $trip)
    {
        try {
            $trip = $this->tripService->getVendorTrip($trip);
            if (!$trip) {
                return $this->error(ApiError::FORBIDDEN);
            }
            return $this->success(new TripResource($trip->load(['stops'])), ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function update(UpdateTripRequest $request, Trips $trip)
    {
        try {
            $updated = $this->tripService->updateTrip($trip, $request->validated());
            return $this->success(new TripResource($updated), ApiSuccess::TRIP_UPDATED);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function destroy(Trips $trip)
    {
        try {
            if (request()->boolean('hard')) {
                $this->tripService->hardDeleteTrip($trip);
                return $this->success(null, ApiSuccess::TRIP_CANCELLED);
            }
            $this->tripService->cancelTrip($trip);
            return $this->success(null, ApiSuccess::TRIP_CANCELLED);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }
}
