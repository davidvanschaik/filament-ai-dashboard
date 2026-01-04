<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class BaseStorageService
{
    protected function countTotalObjectAndBytes(array $response, int &$count, int &$countBytes, string $arrayKey): void
    {
        if (empty($response['data'])) {
            Log::error('Empty response');
            return;
        }

        foreach ($response['data'] as $object) {
            $count++;
            $countBytes += $object[$arrayKey];
        }
    }

    protected function cacheData(string $key, int $count, int $bytes): void
    {
        Cache::forever($key, [
            'count' => $count,
            'bytes' => $this->convertBytes($bytes),
            'time' => now()->timestamp
        ]);
    }

    // Convert bytes to KB/MB/GB/TB.
    protected function convertBytes(int $bytes): string
    {
        $units = ['KB', 'MB', 'GB', 'TB'];

        if ($bytes < 1024) {
            return '0 KB';
        }

        $power = floor(log($bytes, 1024));
        $value = $bytes / pow(1024, $power);

        return round($value, 2) . ' BaseStorageService.php' . $units[$power - 1];
    }
}
