<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'departure_time' => $this->departure_datetime,
            'arrival_time' => $this->arrival_datetime,
            'price' => $this->base_price,
            'vendor_name' => $this->whenLoaded('vendorRoute', fn() => $this->vendorRoute->vendor->user->name),
            'coach_identifier' => $this->whenLoaded('coaches', fn() => $this->coaches->first()->identifier ?? 'N/A'),
        ];
    }
}