<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use DavidvanSchaik\FilamentAiDashboard\Jobs\FetchFileJob;
use DavidvanSchaik\FilamentAiDashboard\Jobs\FetchVectorStoreJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StorageService
{
    public function getStorageData(): array
    {
        $vector = Cache::get('vector_store');
        $files = Cache::get('files');

        if ($this->isExpired($vector) || $this->isExpired($files)) {
            $this->updateCacheData();
            Log::error('Vector store data or files data is not found. Running jobs');
        }

        if (! $vector || ! $files) {
            return ['Error' => 'No data found.'];
        }

        return [
            'Vector stores' => $vector,
            'Uploaded files' => $files,
        ];
    }

    // Check if cache is older than 2 hours.
    private function isExpired(?array $cacheData): bool
    {
        if (! $cacheData || ! isset($cacheData['time'])) {
            return true;
        }

        return now()->timestamp - $cacheData['time'] > 7200;
    }

    public function updateCacheData(): void
    {
        FetchVectorStoreJob::dispatch();
        FetchFileJob::dispatch();
    }
}
