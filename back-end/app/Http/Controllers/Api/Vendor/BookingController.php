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
use Illuminate\Support\Facades\DB;
use App\Models\TripSeats;

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
                    'details.pickupStop:id,name',
                    'details.dropoffStop:id,name',
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
                'trip.seats' => function($q){ $q->with(['coach:id,coach_type']); },
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

    public function update(Request $request, Bookings $booking)
    {
        try {
            $vendorId = optional(Auth::user()->vendor)->id;
            if(!$vendorId){ return $this->error(ApiError::FORBIDDEN); }
            $owns = $booking->trip && $booking->trip->vendorRoute && $booking->trip->vendorRoute->vendor_id === $vendorId;
            if(!$owns){ return $this->error(ApiError::FORBIDDEN); }

            $data = $request->validate([
                'status' => ['sometimes','in:confirmed,cancelled,pending'],
                'pickup_stop_id' => ['sometimes','integer','exists:stops,id'],
                'dropoff_stop_id' => ['sometimes','integer','exists:stops,id'],
                'seat_ids' => ['sometimes','array','min:1'],
                'seat_ids.*' => ['integer'],
            ]);

            if(isset($data['status'])){ $booking->status = $data['status']; }
            $booking->save();

            if(array_key_exists('pickup_stop_id', $data) || array_key_exists('dropoff_stop_id', $data)){
                $booking->details()->update(array_filter([
                    'pickup_stop_id' => $data['pickup_stop_id'] ?? null,
                    'dropoff_stop_id' => $data['dropoff_stop_id'] ?? null,
                ], fn($v) => !is_null($v)));
            }

            if(!empty($data['seat_ids'])){
                DB::transaction(function() use ($booking, $data){
                    $tripId = $booking->trip_id;
                    $newSeatIds = array_values(array_unique(array_map('intval', $data['seat_ids'])));
                    $conflict = TripSeats::where('trip_id', $tripId)
                        ->whereIn('seat_id', $newSeatIds)
                        ->where('status', '!=', 'available')
                        ->exists();
                    if($conflict){
                        throw new \RuntimeException('SEAT_STATE_CONFLICT');
                    }
                    $oldSeatIds = $booking->details()->pluck('seat_id')->all();
                    if($oldSeatIds){
                        TripSeats::where('trip_id', $tripId)->whereIn('seat_id', $oldSeatIds)->update(['status' => 'available']);
                    }
                    $booking->details()->delete();
                    $priceMap = TripSeats::where('trip_id', $tripId)->whereIn('seat_id', $newSeatIds)->pluck('price','seat_id');
                    foreach ($newSeatIds as $sid){
                        $booking->details()->create([
                            'trip_id' => $tripId,
                            'seat_id' => $sid,
                            'price_at_booking' => (float) ($priceMap[$sid] ?? 0),
                            'pickup_stop_id' => null,
                            'dropoff_stop_id' => null,
                        ]);
                    }
                    TripSeats::where('trip_id', $tripId)->whereIn('seat_id', $newSeatIds)->update(['status' => 'booked']);
                });
                \Illuminate\Support\Facades\DB::afterCommit(function() use ($booking){
                    app(\App\Services\Elasticsearch\SeatAvailabilityAggregator::class)->push((int)$booking->trip_id);
                });
            }

            $booking->load([
                'user:id,name,email,phone_number',
                'trip.vendorRoute.route.origin:id,name',
                'trip.vendorRoute.route.destination:id,name',
                'trip.seats' => function($q){ $q->with(['coach:id,coach_type']); },
                'details.seat:id,seat_number',
                'details.pickupStop:id,name',
                'details.dropoffStop:id,name',
            ])->loadCount('details as seat_count');

            return $this->success(new BookingResource($booking), ApiSuccess::ACTION_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function destroy(Bookings $booking)
    {
        try {
            $vendorId = optional(Auth::user()->vendor)->id;
            if(!$vendorId){ return $this->error(ApiError::FORBIDDEN); }
            $owns = $booking->trip && $booking->trip->vendorRoute && $booking->trip->vendorRoute->vendor_id === $vendorId;
            if(!$owns){ return $this->error(ApiError::FORBIDDEN); }

            $booking->status = 'cancelled';
            $booking->save();

            $seatIds = $booking->details()->pluck('seat_id')->all();
            if($seatIds){
                \App\Models\TripSeats::where('trip_id', $booking->trip_id)
                    ->whereIn('seat_id', $seatIds)
                    ->update(['status' => 'available']);
            }

            \Illuminate\Support\Facades\DB::afterCommit(function() use ($booking){
                app(\App\Services\Elasticsearch\SeatAvailabilityAggregator::class)->push((int)$booking->trip_id);
            });

            return $this->success(null, ApiSuccess::BOOKING_CANCELLED);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }
}
