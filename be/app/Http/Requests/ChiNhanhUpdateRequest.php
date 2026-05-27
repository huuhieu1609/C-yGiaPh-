<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChiNhanhUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:chi_nhanhs,id',
            'ten_chi' => 'required|string|max:255',
            'mo_ta' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID chi nhánh không được để trống',
            'id.exists' => 'ID chi nhánh không tồn tại',
            'ten_chi.required' => 'Tên chi nhánh không được để trống',
            'ten_chi.string' => 'Tên chi nhánh phải là chuỗi',
            'ten_chi.max' => 'Tên chi nhánh không được vượt quá 255 ký tự',
            'mo_ta.required' => 'Mô tả không được để trống',
            'mo_ta.string' => 'Mô tả phải là chuỗi',
            'mo_ta.max' => 'Mô tả không được vượt quá 255 ký tự',
        ];
    }
}
