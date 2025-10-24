<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorDetailResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'vendor_id' => $this->id,
            
            
            'company_info' => [
                'company_name' => $this->company_name,
                'address' => $this->address,
                'status' => $this->status,
                'logo_url' => $this->logo_url, 
                
                'total_trips' => $this->vendorRoutes->count(),
                'rating' => 4.8, 
            ],
            
            
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