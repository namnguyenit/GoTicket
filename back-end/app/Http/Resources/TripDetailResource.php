<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $tripSeatStatusMap = $this->whenLoaded('seats')->keyBy('id');

        return [
            'id' => $this->id,
            'departure_datetime' => $this->departure_datetime
                ? $this->departure_datetime->copy()->utc()->format('Y-m-d\TH:i:s.u\Z')
                : null,
            'arrival_datetime' => $this->arrival_datetime
                ? $this->arrival_datetime->copy()->utc()->format('Y-m-d\TH:i:s.u\Z')
                : null,
            'vendor_name' => $this->vendorRoute->vendor->user->name,
            'imageLink' => optional($this->vendorRoute->vendor)->logo_url, 
            'coaches' => $this->whenLoaded('coaches', function () use ($tripSeatStatusMap) {

                return $this->coaches->map(function ($coach) use ($tripSeatStatusMap) {
                    return [
                        'id' => $coach->id,
                        'identifier' => $coach->identifier,
                        'coach_type' => $coach->coach_type,
                        'total_seats' => $coach->total_seats,
                        'seats' => $coach->seats->map(function ($seat) use ($tripSeatStatusMap) {
                            $statusInfo = $tripSeatStatusMap->get($seat->id);
                            return [
                                'id' => $seat->id,
                                'seat_number' => $seat->seat_number,
                                'status' => $statusInfo->pivot->status ?? 'unavailable',
                                'price' => $statusInfo->pivot->price ?? $this->base_price,
                            ];
                        }),
                    ];
                });
            }),
        ];
    }
}