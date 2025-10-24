<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $origin = optional(optional($this->vendorRoute)->route)->origin;
        $destination = optional(optional($this->vendorRoute)->route)->destination;
        $tripName = trim(($origin->name ?? '') . ' - ' . ($destination->name ?? ''));

        return [
            'id' => $this->id,

            'trip' => $tripName !== ' - ' ? $tripName : null,

            'imageLink' => $this->resolveImageUrl(),

            'pickTake' => $this->resolvePickTake(),

            'departureDate' => $this->iso($this->departure_datetime),

            'emptyNumber' => $this->when(isset($this->empty_number), (int) ($this->empty_number ?? 0)),

            'vendorName' => $this->whenLoaded('vendorRoute', function () {
                return optional($this->vendorRoute->vendor->user)->name;
            }),

            'vendorType' => $this->whenLoaded('coaches', function () {
                $vehicle = optional($this->coaches->first())->vehicle;
                return $vehicle->vehicle_type ?? null;
            }),

            'price' => $this->base_price,
        ];
    }

    protected function resolveImageUrl(): ?string
    {
        $vendor = optional($this->vendorRoute)->vendor;
        return $vendor?->logo_url ? (string) $vendor->logo_url : null;
    }

    protected function resolvePickTake(): ?string
    {

        return null;
    }
    protected function iso($dt): ?string{
        if (!$dt) return null;
        $c=$dt instanceof Carbon ? $dt : Carbon::parse($dt);
        return $c->copy()->utc()->format('Y-m-d\TH:i:s.u\Z');
    }
}
