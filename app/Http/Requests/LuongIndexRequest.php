<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LuongIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cho phép gọi API
    }

    public function rules(): array
    {
        return [
            'thang' => 'nullable|integer|min:1|max:12',
            'nam' => 'nullable|integer|min:2000|max:2100',
            'nhan_vien_id' => 'nullable|integer|exists:NhanVien,Id',
        ];
    }
}