<?php

namespace App\Http\Requests;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // [PERBAIKAN] Disederhanakan, hanya untuk user/resident
        return [
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string|max:500',
            'visibility' => ['required', Rule::enum(ReportVisibilityEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'report_category_id.required' => 'Kategori laporan wajib dipilih.',
            'title.required' => 'Judul laporan wajib diisi.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'image.required' => 'Gambar bukti wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau svg.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'latitude.required' => 'Latitude wajib diisi.',
            'longitude.required' => 'Longitude wajib diisi.',
            'address.required' => 'Alamat kejadian wajib diisi.',
            'visibility.required' => 'Opsi visibilitas laporan wajib dipilih.',
        ];
    }
}