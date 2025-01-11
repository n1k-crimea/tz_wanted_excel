<?php

namespace App\Http\Controllers;

use App\Models\Row;
use Illuminate\Http\JsonResponse;

class RowController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Row::all()
            ->groupBy('date')
            ->map(function ($group) {
                return $group->map(function ($row) {
                    return [
                        'id' => $row->row_id,
                        'name' => $row->name,
                    ];
                });
            });

        return response()->json($rows);
    }
}
