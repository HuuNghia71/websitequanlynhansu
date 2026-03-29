<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLuongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NhanVienId' => 'required|exists:NhanVien,Id',
            'Thang' => 'required|integer|min:1|max:12',
            'Nam' => 'required|integer|min:2000|max:2100',
            'LuongCoBan' => 'required|numeric|min:0',

            // ❌ bỏ required
            'TongNgayCong' => 'nullable',
            'Thuong' => 'nullable',
            'Phat' => 'nullable',
        ];
    }
}