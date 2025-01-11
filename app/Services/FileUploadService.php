<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * @param UploadedFile $file
     * @return string
     */
    public function storeFile(UploadedFile $file): string
    {
        return $file->store('uploads');
    }
}
