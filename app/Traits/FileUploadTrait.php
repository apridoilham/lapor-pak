<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    public function handleFileUpload(Request $request, string $inputName, string $path, string $oldFilePath = null): ?string
    {
        if (!$request->hasFile($inputName)) {
            return null;
        }

        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        $file = $request->file($inputName);
        return $file->store($path, 'public');
    }
}