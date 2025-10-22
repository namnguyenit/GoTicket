<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'status' => $this->status,
            'owner' => [
                'id' => $this->whenLoaded('user', fn() => $this->user?->id),
                'name' => $this->whenLoaded('user', fn() => $this->user?->name),
                'email' => $this->whenLoaded('user', fn() => $this->user?->email),
                'phone_number' => $this->whenLoaded('user', fn() => $this->user?->phone_number),
            ],
            'counts' => [
                'vehicles' => $this->when(isset($this->vehicles_count), fn() => (int) $this->vehicles_count),
                'routes' => $this->when(isset($this->vendor_routes_count), fn() => (int) $this->vendor_routes_count),
            ],
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
