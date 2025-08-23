<?php

namespace App\Http\Requests;

use App\Enums\ReportStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertReportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_id'   => 'required|exists:reports,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => ['required', 'string', Rule::enum(ReportStatusEnum::class)],
            'description' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'report_id.required'    => 'ID Laporan tidak boleh kosong.',
            'report_id.exists'      => 'ID Laporan tidak ditemukan.',
            'image.image'           => 'File harus berupa gambar.',
            'image.mimes'           => 'Format gambar yang didukung adalah jpeg, png, dan jpg.',
            'image.max'             => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
            'status.required'       => 'Status laporan wajib diisi.',
            'status.string'         => 'Status laporan harus berupa teks.',
            'status.enum'           => 'Status yang dipilih tidak valid.',
            'description.required'  => 'Deskripsi atau catatan wajib diisi.',
            'description.max'       => 'Deskripsi tidak boleh lebih dari 5000 karakter.',
        ];
    }
}