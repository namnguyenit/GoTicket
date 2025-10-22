<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $origin = optional(optional($this->vendorRoute)->route)->origin;
        $destination = optional(optional($this->vendorRoute)->route)->destination;

        $vehicle = optional($this->coaches->first())->vehicle; // assume all coaches share same vehicle on vendor trip

        return [
            'id' => $this->id,
            'vendor_route_id' => $this->vendor_route_id,
            'departure_datetime' => $this->departure_datetime?->toISOString(),
            'arrival_datetime' => $this->arrival_datetime?->toISOString(),
            'base_price' => $this->base_price,
            'train_prices' => $this->when(($vehicle && ($vehicle->vehicle_type ?? null) === 'train'), function(){
                $regular = null; $vip = null;
                if ($this->relationLoaded('seats')) {
                    foreach ($this->seats as $seat) {
                        $coachType = optional($seat->coach)->coach_type;
                        $price = (float) ($seat->pivot->price ?? 0);
                        if ($coachType === 'seat_VIP') { $vip = $vip ?? $price; }
                        else { $regular = $regular ?? $price; }
                        if ($regular !== null && $vip !== null) break;
                    }
                }
                return ['regular' => $regular, 'vip' => $vip];
            }),
            'status' => $this->status,
            'route' => [
                'origin' => $origin->name ?? null,
                'destination' => $destination->name ?? null,
                'label' => (($origin->name ?? null) && ($destination->name ?? null)) ? (($origin->name).' - '.($destination->name)) : null,
            ],
            'vehicle' => $this->when($vehicle, function() use ($vehicle){
                return [
                    'name' => $vehicle->name,
                    'vehicle_type' => $vehicle->vehicle_type,
                    'license_plate' => $vehicle->license_plate,
                ];
            }),
            'capacity' => $this->coaches?->sum('total_seats') ?? null,
            'empty_number' => $this->when(isset($this->empty_number), (int) $this->empty_number),
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
