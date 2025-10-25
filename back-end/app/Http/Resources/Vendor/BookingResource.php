<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $route = $this->trip?->vendorRoute?->route;
        $origin = $route?->origin?->name;
        $dest   = $route?->destination?->name;
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'phone_number' => $this->user?->phone_number,
            ],
            'trip' => [
                'id' => $this->trip_id,
                'route' => $origin && $dest ? ($origin.' - '.$dest) : null,
                'departure_datetime' => $this->trip?->departure_datetime,
                'arrival_datetime' => $this->trip?->arrival_datetime,
                'vehicle' => ($veh = optional(optional($this->trip)->coaches)->first()?->vehicle) ? [
                    'name' => $veh->name,
                    'vehicle_type' => $veh->vehicle_type,
                    'license_plate' => $veh->license_plate,
                ] : null,
            ],
            'seat_count' => $this->seat_count ?? ($this->whenCounted('details')),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'stops' => [
                'pickup' => optional($this->details->first())->pickupStop?->name,
                'dropoff' => optional($this->details->first())->dropoffStop?->name,
            ],
            'seats' => $this->whenLoaded('details', function(){
                return $this->details->map(function($d){
                    return [
                        'seat_number' => optional($d->seat)->seat_number,
                    ];
                });
            }),
        ];
    }
}
