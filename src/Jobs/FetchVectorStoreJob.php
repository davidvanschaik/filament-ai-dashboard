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

class FetchVectorStoreJob extends BaseStorageService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        set_time_limit(0);
        Log::info('Fetching Vector Store');

        $client = app(OpenAiClient::class);

        $this->fetchAllVectorStores($client);
    }

    private function fetchAllVectorStores(OpenAiCLient $client): void
    {
        $countStores = 0;
        $countBytes = 0;
        $after = '';

        do {
            $response = $this->fetchVectorStores($client, $after);

            $this->countTotalObjectAndBytes($response, $countStores, $countBytes, 'usage_bytes');

            $after = $response['last_id'];

        } while (count($response['data']) === 100 || $response['has_more']);

        $this->cacheData('vector_store', $countStores, $countBytes);
        Log::info('Vector stores successfully fetched!');
    }

    private function fetchVectorStores(OpenAiClient $client, string $after): array
    {
        try {
            return $client->getVectorStores($after);
        } catch (Throwable $e) {
            Log::error('Error fetching Vector Stores: ' . $e->getMessage());
            return [];
        }
    }
}
