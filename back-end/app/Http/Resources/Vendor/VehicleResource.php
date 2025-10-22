<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'license_plate' => $this->license_plate,
            'vehicle_type' => $this->vehicle_type,
            'capacity' => (int) ($this->coaches ? $this->coaches->sum('total_seats') : ($this->coaches()->sum('total_seats') ?? 0)),
            'coaches' => $this->whenLoaded('coaches', function(){
                return $this->coaches->map(function($c){
                    return [
                        'id' => $c->id,
                        'identifier' => $c->identifier,
                        'coach_type' => $c->coach_type,
                        'total_seats' => (int) $c->total_seats,
                    ];
                });
            }),
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
