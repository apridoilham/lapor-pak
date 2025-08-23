<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        $categoryId = $this->route('report_category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('report_categories')->ignore($categoryId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori laporan wajib diisi.',
            'name.string' => 'Nama kategori laporan harus berupa teks.',
            'name.max' => 'Nama kategori laporan tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama kategori laporan sudah ada.',
        ];
    }
}