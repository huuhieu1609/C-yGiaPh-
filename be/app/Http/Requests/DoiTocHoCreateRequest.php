<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DoiTocHoCreateRequest extends FormRequest
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
            'so_doi' => 'required|unique:doi_toc_hos,so_doi',
            'ten_doi' => 'required',
            'mo_ta' => 'required',
            'trang_thai' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'so_doi.required' => 'Số đời không được để trống',
            'ten_doi.required' => 'Tên đời không được để trống',
            'mo_ta.required' => 'Mô tả không được để trống',
            'trang_thai.required' => 'Trạng thái không được để trống',
        ];
    }
}
