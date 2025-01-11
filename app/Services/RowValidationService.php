<?php

namespace App\Services;

class RowValidationService
{
    public function validateRow(array $row): array
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
}
