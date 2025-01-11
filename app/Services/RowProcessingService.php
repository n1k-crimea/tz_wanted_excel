<?php

namespace App\Services;

use App\Models\Row;
use App\Events\RowCreated;

class RowProcessingService
{
    public function processRow(array $row): void
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
        }
    }
}
