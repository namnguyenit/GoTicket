<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Trả về mảng dữ liệu cơ bản
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone_number,
            'email' => $this->email,
            'role' => $this->role,
            
            // Dùng mergeWhen() để chỉ thêm trường 'status' một cách an toàn
            // khi role là 'vendor' VÀ mối quan hệ 'vendor' đã được tải.
            $this->mergeWhen($this->whenLoaded('vendor'), [
                'status' => optional($this->vendor)->status
            ]),

            'created_at' => $this->created_at
                ? $this->created_at->copy()->utc()->format('Y-m-d\\TH:i:s.u\\Z')
                : null,
        ];
    }
}