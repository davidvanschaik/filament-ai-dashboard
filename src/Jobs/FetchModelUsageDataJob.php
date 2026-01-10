<?php

namespace DavidvanSchaik\FilamentAiDashboard\Jobs;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAiClient;
use DavidvanSchaik\FilamentAiDashboard\Services\BaseStorageService;
use DavidvanSchaik\FilamentAiDashboard\Services\OpenAiService;
use DavidvanSchaik\FilamentAiDashboard\Services\TimeRangeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchModelUsageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $service = app(TimeRangeService::class);
        $client = app(OpenAiClient::class);

        $this->getModelDataThisMonth($service, $client);
    }

    private function getModelDataThisMonth(TimeRangeService $service, OpenAiClient $client): void
    {
        $cacheKey = 'ai_model_usage_this_month';

        [$start, $end] = $service->getThisMonthTimestamps();
        $models = $this->fetchAndFilter($client, $start, $end);

        Cache::forever($cacheKey, [
            'models' => $models,
            'time' => now()->timestamp
        ]);

        $this->getModelDataFromAllOtherMonths($service, $client);
    }

    private function getModelDataFromAllOtherMonths(TimeRangeService $service, OpenAiClient $client): void
    {
        $modelsTotal = [];
        $month  = Carbon::now('UTC')->copy()->subMonthNoOverflow()->startOfMonth();

        while (true) {
            [$start, $end] = $service->getMonthTimeStamps($month);
            $models = $this->fetchAndFilter($client, $start, $end);

            $modelsTotal[] = $models;

            if (count($models) === 0) {
                break;
            }

            // Merge models into the total aggregate
            foreach ($models as $modelKey => $data) {
                $modelsTotal[$modelKey] = $modelsTotal[$modelKey] ?? [
                    'requests' => 0,
                    'input_tokens' => 0,
                    'cached_tokens' => 0,
                    'output_tokens' => 0,
                ];

                $modelsTotal[$modelKey]['requests'] += $data['requests'];
                $modelsTotal[$modelKey]['input_tokens'] += $data['input_tokens'];
                $modelsTotal[$modelKey]['cached_tokens'] += $data['cached_tokens'];
                $modelsTotal[$modelKey]['output_tokens'] += $data['output_tokens'];
            }

            $month->subMonthNoOverflow()->startOfMonth();
        }

        Cache::forever('ai_model_usage_total', $modelsTotal);
    }

    private function fetchAndFilter(OpenAiClient $client, int $start, int $end): array
    {
        $models = [];
        $response = $client->getUsage($start, $end);

        foreach ($response['data'] as $buckets) {
            $this->filterBuckets($buckets, $models);
        }

        return $models;
    }

    private function filterBuckets(array $buckets, array &$models): void
    {
        if (! isset($buckets['results']) || ! is_array($buckets['results'])) {
            Log::warning('AiModelService: No buckets found');
            return;
        }

        foreach ($buckets['results'] as $result) {
            $model = $result['model'];

            $models[$model] = $models[$model] ?? [
                'requests' => 0,
                'input_tokens' => 0,
                'cached_tokens' => 0,
                'output_tokens' => 0,
            ];

            $models[$model]['requests'] += $result['num_model_requests'];
            $models[$model]['input_tokens'] += $result['input_tokens'] - $result['input_cached_tokens'];
            $models[$model]['cached_tokens'] += $result['input_cached_tokens'];
            $models[$model]['output_tokens'] += $result['output_tokens'];
        }
    }

}
