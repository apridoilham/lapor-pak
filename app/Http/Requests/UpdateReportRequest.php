<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resident_id' => 'required|exists:residents,id',
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            // ▼▼▼ PERUBAHAN DI SINI ▼▼▼
            'description' => 'required|string|max:5000',
            'image' => 'nullable|file|image|max:2048', // Image tidak wajib, tapi jika ada harus valid
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string|max:500'
        ];
    }
}