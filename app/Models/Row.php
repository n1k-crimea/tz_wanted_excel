<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    use HasFactory;

    protected $table = 'rows';

    protected $fillable = [
        'row_id',
        'name',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
