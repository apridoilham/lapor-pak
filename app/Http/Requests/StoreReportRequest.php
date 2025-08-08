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
            'description' => 'required|string',
            'image' => 'required|file',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'address' => 'required|string',
        ];

        // PERUBAHAN DI SINI:
        // Logika ternary diganti dengan blok if/else yang lebih mudah dibaca.
        if (auth()->user()->hasRole('admin')) {
            $rules['resident_id'] = 'required|exists:residents,id';
        } else {
            // Untuk user biasa (resident), resident_id tidak diisi dari form, jadi nullable.
            $rules['resident_id'] = 'nullable|exists:residents,id';
        }

        return $rules;
    }
}