<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'vendor_id' => $this->id,
            
            // Thông tin công ty (Vendor Model)
            'company_info' => [
                'company_name' => $this->company_name,
                'address' => $this->address,
                'status' => $this->status,
                // Giả định bạn có thông tin đánh giá (Reviews) và số vé đã bán (Bookings)
                'total_trips' => $this->vendorRoutes->count(),
                'rating' => 4.8, // Placeholder
            ],
            
            // Thông tin người đại diện (User Model)
            'representative_info' => [
                'user_id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone_number' => $this->user->phone_number,
                'role' => $this->user->role,
                'joined_at' => $this->user->created_at,
            ],
        ];
    }
}