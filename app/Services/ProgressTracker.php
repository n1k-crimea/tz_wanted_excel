<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class ProgressTracker
{
    public function updateProgress(string $key): void
    {
        $current = Redis::get($key) ?? 0;
        Redis::set($key, $current + 1);
    }
}
