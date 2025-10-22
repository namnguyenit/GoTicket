<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $origin = optional(optional($this->vendorRoute)->route)->origin;
        $destination = optional(optional($this->vendorRoute)->route)->destination;
        $tripName = trim(($origin->name ?? '') . ' - ' . ($destination->name ?? ''));

        return [
            'id' => $this->id,
            // Tên tuyến hiển thị "Đi - Đến"
            'trip' => $tripName !== ' - ' ? $tripName : null,
            // Link ảnh: tạm thời dùng placeholder method, bạn sẽ cài đặt sau
            'imageLink' => $this->resolveImageUrl(),
            // Điểm đón (pickTake): placeholder, bạn sẽ cài đặt sau
            'pickTake' => $this->resolvePickTake(),
            // Ngày/giờ khởi hành (ISO 8601 có microseconds, UTC Z)
            'departureDate' => $this->iso($this->departure_datetime),
            // Số ghế trống (được tính ở withCount -> alias empty_number)
            'emptyNumber' => $this->when(isset($this->empty_number), (int) ($this->empty_number ?? 0)),
            // Tên nhà xe
            'vendorName' => $this->whenLoaded('vendorRoute', function () {
                return optional($this->vendorRoute->vendor->user)->name;
            }),
            // Loại xe từ coaches.vehicle
            'vendorType' => $this->whenLoaded('coaches', function () {
                $vehicle = optional($this->coaches->first())->vehicle;
                return $vehicle->vehicle_type ?? null;
            }),
            // Giá cơ bản
            'price' => $this->base_price,
        ];
    }

    // Placeholder: Trả về URL ảnh minh họa cho trip. TODO: cài đặt logic trả ảnh thật.
    protected function resolveImageUrl(): ?string
    {
        // Ví dụ sau này có thể lấy từ vehicle/vendedor hoặc CDN
        return null;
    }

    // Placeholder: Trả về thông tin điểm đón nổi bật để hiển thị ở danh sách
    protected function resolvePickTake(): ?string
    {
        // Ví dụ: lấy stop pickup đầu tiên khi đã eager load stops
        return null;
    }
    protected function iso($dt): ?string{
        if (!$dt) return null;
        $c=$dt instanceof Carbon ? $dt : Carbon::parse($dt);
        return $c->copy()->utc()->format('Y-m-d\TH:i:s.u\Z');
    }
}
