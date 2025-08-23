<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:report_categories,name',
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
