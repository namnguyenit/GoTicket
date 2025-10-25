<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookings;
use App\Http\Resources\Vendor\BookingResource;

class BookingController extends Controller
{
    use ResponseHelper;

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->query('per_page', 10);
            $vendorId = optional(Auth::user()->vendor)->id;
            if(!$vendorId){
                return $this->error(ApiError::FORBIDDEN);
            }

            $paginator = Bookings::query()
                ->whereHas('trip.vendorRoute', function($q) use ($vendorId){
                    $q->where('vendor_id', $vendorId);
                })
                ->with([
                    'user:id,name,email,phone_number',
                    'trip.vendorRoute.route.origin:id,name',
                    'trip.vendorRoute.route.destination:id,name',
                    'trip.coaches.vehicle',
                    'details.seat:id,seat_number,coach_id',
                ])
                ->withCount(['details as seat_count'])
                ->orderByDesc('id')
                ->paginate($perPage);

            $data = [
                'data' => BookingResource::collection($paginator->items()),
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

    public function show(Bookings $booking)
    {
        try {
            $vendorId = optional(Auth::user()->vendor)->id;
            if(!$vendorId){
                return $this->error(ApiError::FORBIDDEN);
            }
            $owns = $booking->trip && $booking->trip->vendorRoute && $booking->trip->vendorRoute->vendor_id === $vendorId;
            if(!$owns){
                return $this->error(ApiError::FORBIDDEN);
            }
            $booking->load([
                'user:id,name,email,phone_number',
                'trip.vendorRoute.route.origin:id,name',
                'trip.vendorRoute.route.destination:id,name',
                'details.seat:id,seat_number',
                'details.pickupStop:id,name',
                'details.dropoffStop:id,name',
            ])->loadCount('details as seat_count');
            return $this->success(new BookingResource($booking), ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }
}
