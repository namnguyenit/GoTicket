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
            'logo_url' => $this->when(isset($this->logo_url), function() use ($request){
                $url = (string) $this->logo_url;
                if(!$url){ return null; }
                if(str_starts_with($url,'http://') || str_starts_with($url,'https://')) return $url;
                $url = '/'.ltrim($url,'/');
                $url = str_replace('/storage/','/files/public/',$url);
                $base = rtrim($request->getSchemeAndHttpHost(),'/');
                return $base.$url;
            }), 
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
