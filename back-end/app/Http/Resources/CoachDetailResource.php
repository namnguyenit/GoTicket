<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'coach_type' => $this->coach_type,
            'total_seats' => $this->total_seats,
            // 'seats' sẽ được xử lý ở TripDetailResource
        ];
    }
}