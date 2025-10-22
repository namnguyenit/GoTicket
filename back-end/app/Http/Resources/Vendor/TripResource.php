<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_route_id' => $this->vendor_route_id,
            'departure_datetime' => $this->departure_datetime?->toISOString(),
            'arrival_datetime' => $this->arrival_datetime?->toISOString(),
            'base_price' => $this->base_price,
            'status' => $this->status,
            'stops' => $this->whenLoaded('stops', function () {
                return $this->stops->map(function ($stop) {
                    return [
                        'id' => $stop->id,
                        'name' => $stop->name,
                        'address' => $stop->address,
                        'location_id' => $stop->location_id,
                        'pivot' => [
                            'stop_type' => $stop->pivot->stop_type,
                            'scheduled_time' => $stop->pivot->scheduled_time
                                ? \Carbon\Carbon::parse($stop->pivot->scheduled_time)->toISOString()
                                : null,
                        ],
                    ];
                });
            }),
        ];
    }
}
