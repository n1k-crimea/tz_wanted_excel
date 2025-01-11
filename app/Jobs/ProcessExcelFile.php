<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RowValidationService;
use App\Services\RowProcessingService;
use App\Services\ErrorLoggerService;
use App\Services\ProgressTracker;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected string $redisKey;

    private RowValidationService $validationService;
    private RowProcessingService $rowProcessingService;
    private ErrorLoggerService $errorLogger;
    private ProgressTracker $progressTracker;

    public function __construct(
        string $filePath,
        string $redisKey
    ) {
        $this->filePath = $filePath;
        $this->redisKey = $redisKey;

        $this->validationService = app(RowValidationService::class);
        $this->rowProcessingService = app(RowProcessingService::class);
        $this->errorLogger = app(ErrorLoggerService::class);
        $this->progressTracker = app(ProgressTracker::class);
    }

    public function handle(): void
    {
        $spreadsheet = IOFactory::load(storage_path('app/' . $this->filePath));
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        unset($rows[0]);

        $chunkSize = 1000;
        $chunks = array_chunk($rows, $chunkSize);

        foreach ($chunks as $chunkIndex => $chunk) {
            foreach ($chunk as $rowIndex => $row) {
                $lineNumber = ($chunkIndex * $chunkSize) + $rowIndex + 2;

                $errors = $this->validationService->validateRow($row);
                if (!empty($errors)) {
                    $this->errorLogger->logError($lineNumber, $errors);
                    continue;
                }

                $this->rowProcessingService->processRow($row);

                $this->progressTracker->updateProgress($this->redisKey);
            }
        }

        $this->errorLogger->saveErrors();
    }
}
