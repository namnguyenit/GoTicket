<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Vehicles;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class CreateTicketRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required','integer','exists:vehicles,id'],
            'start_date' => ['required','date'],
            'start_time' => ['required','regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/'],
            'from_city'  => ['required','string','min:1'],
            'to_city'    => ['required','string','min:1','different:from_city'],
            // price only required for bus; optional otherwise
            'price'         => ['sometimes','numeric','between:0,99999999.99'],
            // train-specific prices
            'regular_price' => ['sometimes','numeric','between:0,99999999.99'],
            'vip_price'     => ['sometimes','numeric','between:0,99999999.99'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $vehicleId = $this->input('vehicle_id');
            if(!$vehicleId){ return; }
            $type = Vehicles::query()->where('id', $vehicleId)->value('vehicle_type');
            if($type === 'bus'){
                if(!$this->filled('price')){
                    $v->errors()->add('price', 'Giá vé là bắt buộc cho xe bus.');
                }
            } elseif($type === 'train'){
                if(!$this->filled('regular_price')){
                    $v->errors()->add('regular_price', 'Giá toa thường là bắt buộc cho tàu hoả.');
                }
                if(!$this->filled('vip_price')){
                    $v->errors()->add('vip_price', 'Giá toa VIP là bắt buộc cho tàu hoả.');
                }
            }
        });
    }
}
