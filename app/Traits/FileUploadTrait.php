<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait FileUploadTrait
{
    /**
     * Menangani proses unggah file.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $inputName Nama field input file (misal: 'image', 'avatar')
     * @param string $path Direktori penyimpanan di dalam 'storage/app/public'
     * @return string|null Path file yang disimpan atau null jika tidak ada file.
     */
    protected function handleFileUpload(Request $request, string $inputName, string $path): ?string
    {
        if (!$request->hasFile($inputName)) {
            return null;
        }

        return $request->file($inputName)->store($path, 'public');
    }
}