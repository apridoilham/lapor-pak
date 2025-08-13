<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    /**
     * Menangani upload file dari request, menyimpannya, dan menghapus file lama jika ada.
     *
     * @param Request $request Instance dari request yang masuk.
     * @param string $inputName Nama input file dari form.
     * @param string $path Direktori penyimpanan di dalam 'storage/app/public'.
     * @param string|null $oldFilePath Path file lama yang akan dihapus jika ada.
     * @return string|null Path file yang baru disimpan, atau null jika tidak ada file yang diunggah.
     */
    public function handleFileUpload(Request $request, string $inputName, string $path, string $oldFilePath = null): ?string
    {
        if (!$request->hasFile($inputName)) {
            return null;
        }

        // Hapus file lama jika ada
        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        $file = $request->file($inputName);
        return $file->store($path, 'public');
    }
}