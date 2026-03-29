<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLuongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'LuongCoBan' => 'sometimes|numeric|min:0',
            'TongNgayCong' => 'sometimes|numeric|min:0',
            'Thuong' => 'sometimes|numeric|min:0',
            'Phat' => 'sometimes|numeric|min:0',
        ];
    }
}
