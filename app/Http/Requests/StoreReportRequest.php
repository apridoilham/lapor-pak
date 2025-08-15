<?php

namespace App\Http\Requests;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string|max:500',
        ];

        if ($this->user()->hasAnyRole(['admin', 'super-admin'])) {
            $rules['resident_id'] = 'required|exists:residents,id';
        }

        if ($this->user()->hasRole('resident')) {
            $rules['visibility'] = ['required', Rule::enum(ReportVisibilityEnum::class)];
        }

        return $rules;
    }

    /**
     * Dapatkan pesan kesalahan kustom untuk aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resident_id.required' => 'Kolom pelapor wajib dipilih.',
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