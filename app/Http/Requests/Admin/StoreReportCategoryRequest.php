<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Sesuaikan dengan logic otorisasi Anda.
        // Untuk sekarang, kita set true agar request bisa diproses.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:report_categories,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori laporan wajib diisi.',
            'name.string' => 'Nama kategori laporan harus berupa teks.',
            'name.max' => 'Nama kategori laporan tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama kategori laporan sudah ada.',
            'image.required' => 'Gambar kategori laporan wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung adalah jpeg, png, jpg, gif, dan svg.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        ];
    }
}