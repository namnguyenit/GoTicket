<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone_number,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at
                ? $this->created_at->copy()->utc()->format('Y-m-d\\TH:i:s.u\\Z')
                : null,
        ];
    }
}