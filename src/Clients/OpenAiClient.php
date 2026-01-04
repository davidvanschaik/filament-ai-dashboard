<?php

namespace DavidvanSchaik\FilamentAiDashboard\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Class responsible for making API calls to the right endpoints and returning the API response.
class OpenAiClient
{
    public function getUsage(int $start, int $end): array
    {
        $key = config('filament-ai-dashboard-api.usage.key');
        $url = config('filament-ai-dashboard-api.usage.models');

        $params = [
            'start_time' => $start,
            'end_time' => $end,
            'group_by' => 'model',
            'bucket_width' => '1d',
            'limit' => 31,
            'project_ids' => 'proj_Ze4FUyYVaoJfMABJeDGItBDS',
        ];

        return $this->sendRequest($url, $key, $params);
    }

    public function getVectorStores(string $after = ''): array
    {
        $key = config('filament-ai-dashboard-api.storage.key');
        $url = config('filament-ai-dashboard-api.storage.vector_store');

        $params = [
            'limit' => 100,
            'after' => $after,
        ];

        return $this->sendRequest($url, $key, $params);
    }

    public function getFiles(string $after = ''): array
    {
        $key = config('filament-ai-dashboard-api.storage.key');
        $url = config('filament-ai-dashboard-api.storage.files');

        $params = [
            'limit' => 10000,
            'after' => $after,
        ];

        return $this->sendRequest($url, $key, $params);
    }

    private function sendRequest(string $url, ?string $key, array $params): array
    {
        try {
            $response = Http::withToken($key)
                ->acceptJson()
                ->get(url($url), $params);

            $response->throw();
            return $response->json();
        } catch (\Exception $e) {
            Log::error("OpenAI Client Error: {$e->getMessage()}");

            return [];
        }
    }
}
