<?php

namespace App\Jobs;

use App\Events\RowCreated;
use App\Models\Row;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected string $redisKey;

    public function __construct(string $filePath, string $redisKey)
    {
        $this->filePath = $filePath;
        $this->redisKey = $redisKey;
    }

    public function handle(): void
    {
        $spreadsheet = IOFactory::load(storage_path('app/' . $this->filePath));
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        unset($rows[0]);

        $chunkSize = 1000;
        $chunks = array_chunk($rows, $chunkSize);
        $processedCount = 0;
        $validationErrors = [];

        foreach ($chunks as $chunkIndex => $chunk) {
            foreach ($chunk as $rowIndex => $row) {
                $lineNumber = ($chunkIndex * $chunkSize) + $rowIndex + 2;
                $errors = $this->validateRow($row);

                if (!empty($errors)) {
                    $validationErrors[] = "{$lineNumber} - " . implode(', ', $errors);
                    continue;
                }

                if ($this->processRow($row)) {
                    $processedCount++;
                    Redis::set($this->redisKey, $processedCount);
                }
            }
        }

        $this->saveValidationErrors($validationErrors);
    }

    /**
     * @param array $row
     * @return bool
     */
    private function processRow(array $row): bool
    {
        $id = $row[0];
        $name = $row[1];
        $date = \DateTime::createFromFormat('d.m.Y', $row[2]);

        $rowModel = Row::firstOrCreate(
            ['row_id' => $id],
            ['name' => $name, 'date' => $date]
        );

        if ($rowModel->wasRecentlyCreated) {
            broadcast(new RowCreated($rowModel));
            return true;
        }

        return false;
    }

    /**
     * @param array $row
     * @return array
     */
    private function validateRow(array $row): array
    {
        $errors = [];
        $id = $row[0] ?? null;
        $name = $row[1] ?? null;
        $date = $row[2] ?? null;

        if (!is_numeric($id) || $id <= 0) {
            $errors[] = 'ID должен быть положительным числом.';
        }

        if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
            $errors[] = 'Name должен содержать только буквы и пробелы.';
        }

        if (!\DateTime::createFromFormat('d.m.Y', $date)) {
            $errors[] = 'Неверный формат даты (ожидается d.m.Y).';
        }

        return $errors;
    }

    /**
     * @param array $errors
     * @return void
     */
    private function saveValidationErrors(array $errors): void
    {
        if (empty($errors)) {
            return;
        }

        $filePath = storage_path('app/result.txt');
        file_put_contents($filePath, implode(PHP_EOL, $errors));

        echo "Ошибки сохранены в: {$filePath}" . PHP_EOL;
    }
}
