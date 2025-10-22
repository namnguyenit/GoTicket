<?php

namespace App\Http\Requests\Api;

class FindUserByNameRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1',
        ];
    }

    public function validationData(): array
    {
        return $this->query();
    }
}
