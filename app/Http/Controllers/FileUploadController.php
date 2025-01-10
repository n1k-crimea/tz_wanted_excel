<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Jobs\ProcessExcelFile;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function upload(FileUploadRequest $request): JsonResponse
    {
        $filePath = $this->fileUploadService->storeFile($request->file('file'));
        $redisKey = 'file_processing_' . uniqid();

        ProcessExcelFile::dispatch($filePath, $redisKey);

        return response()->json([
            'message' => 'Файл загружен и отправлен на обработку.',
            'redis_key' => $redisKey,
        ]);
    }
}
