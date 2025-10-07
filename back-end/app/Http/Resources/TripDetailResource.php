<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Tạo một map để tra cứu trạng thái ghế nhanh hơn
        $tripSeatStatusMap = $this->whenLoaded('seats')->keyBy('id');

        return [
            'id' => $this->id,
            'departure_datetime' => $this->departure_datetime,
            'arrival_datetime' => $this->arrival_datetime,
            'vendor_name' => $this->vendorRoute->vendor->user->name,
            'coaches' => $this->whenLoaded('coaches', function () use ($tripSeatStatusMap) {
                // Với mỗi coach, ta sẽ thêm thông tin ghế và trạng thái của chúng
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