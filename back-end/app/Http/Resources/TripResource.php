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

             'coaches' => $this->when(
                 optional(optional($this->coaches->first())->vehicle)->vehicle_type === 'train' && $this->relationLoaded('coaches'),
                 function () {
                     return $this->coaches->map(function ($coach) {
                         return [
                             'id' => $coach->id,
                             'identifier' => $coach->identifier,
                             'coach_type' => $coach->coach_type,
                             'total_seats' => (int) $coach->total_seats,
                             'seats' => $coach->relationLoaded('seats')
                                 ? $coach->seats->map(fn($s) => [
                                     'id' => $s->id,
                                     'seat_number' => $s->seat_number,
                                 ])
                                 : null,
                         ];
                     });
                 }
             ),

             'price' => $this->base_price,
        ];
    }

    protected function resolveImageUrl(): ?string
    {
        $vendor = optional($this->vendorRoute)->vendor;
        $url = $vendor?->logo_url;
        if(!$url){ return null; }
        $url = (string) $url;
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $path = parse_url($url, PHP_URL_PATH) ?? $url;
        } else {
            $path = '/'.ltrim($url, '/');
        }
        $path = str_replace('/storage/', '/files/public/', $path);
        $base = rtrim(request()->getSchemeAndHttpHost(), '/');
        return $base . $path;
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
