<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seat_number' => $this->seat_number,
            // Lấy thông tin trạng thái từ pivot table
            'status' => $this->pivot->status ?? 'unavailable',
            'price' => $this->pivot->price ?? null,
        ];
    }
}