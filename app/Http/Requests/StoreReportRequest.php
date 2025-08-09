<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            // ▼▼▼ PERUBAHAN DI SINI ▼▼▼
            'description' => 'required|string|max:5000', // Batasan 5000 karakter
            'image' => 'required|file|image|max:2048', // Tambahkan validasi gambar & ukuran
            'latitude' => 'required|numeric|between:-90,90', // Harus angka antara -90 dan 90
            'longitude' => 'required|numeric|between:-180,180', // Harus angka antara -180 dan 180
            'address' => 'required|string|max:500' // Batasan 500 karakter
        ];

        if (auth()->user()->hasRole('admin')) {
            $rules['resident_id'] = 'required|exists:residents,id';
        } else {
            $rules['resident_id'] = 'nullable|exists:residents,id';
        }

        return $rules;
    }
}