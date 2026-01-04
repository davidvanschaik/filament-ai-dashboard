<?php

namespace DavidvanSchaik\FilamentAiDashboard\Jobs;

use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAiClient;
use DavidvanSchaik\FilamentAiDashboard\Services\BaseStorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchFileJob extends BaseStorageService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        set_time_limit(0);
        Log::info("Fetching files");

        $client = app(OpenAiClient::class);

        $this->fetchAllFiles($client);
    }

    private function fetchAllFiles(OpenAiClient $client): void
    {
        $countFiles = 0;
        $countBytes = 0;
        $after = '';

        do {
            $response = $this->FetchFiles($client, $after);

            $this->countTotalObjectAndBytes($response, $countFiles, $countBytes, 'bytes');

            $after = $response['last_id'];

        } while (count($response['data']) === 10000 || $response['has_more']);

        $this->cacheData('files', $countFiles, $countBytes);
        Log::info('Files successfully fetched!');
    }

    protected function FetchFiles(OpenAiCLient $client, string $after = ''): array
    {
        try {
            return $client->getFiles($after);
        } catch (Throwable $e) {
            Log::error('Error FetchFileJob->FetchFiles(): ' . $e->getMessage());
            return [];
        }
    }
}
