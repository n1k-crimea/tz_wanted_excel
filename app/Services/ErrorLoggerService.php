<?php

namespace App\Services;

class ErrorLoggerService
{
    private array $errors = [];

    public function logError(int $lineNumber, array $messages): void
    {
        $this->errors[] = "{$lineNumber} - " . implode(', ', $messages);
    }

    public function saveErrors(): void
    {
        if (empty($this->errors)) {
            return;
        }

        $filePath = storage_path('app/result.txt');
        file_put_contents($filePath, implode(PHP_EOL, $this->errors));

        echo "Ошибки сохранены в: {$filePath}" . PHP_EOL;
    }
}
