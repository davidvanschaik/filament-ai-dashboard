<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAIClient;
use DavidvanSchaik\FilamentAiDashboard\Jobs\FetchModelUsageDataJob;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

// Abstract class that is used by AiModelService and UsageService for helping handle all the usage data.
abstract class OpenAiService
{
    protected function getUsageData(string $range): array
    {
        try {
            return $this->getDataFromTimeRange($range);

        } catch (ConnectionException $e) {
            Log::error('AiModelService Error: Error in connection: ' . $e->getMessage());
        } catch (RequestException $e) {
            Log::error('AiModelService Error: Invalid request: ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::error('AiModelService Error: unexpected Error by retrieving models: ' . $e->getMessage());
        }

        return [];
    }

    protected function getRawModels(string $month): array
    {
        try {
            $client = app(OpenAiClient::class);
            $service = app(TimeRangeService::class);

            [$start, $end] = $service->parseToTimestamp($month);

            return $client->getUsage($start, $end);

        } catch (ConnectionException $e) {
            Log::error('AiModelService Error: Error in connection: ' . $e->getMessage());
        } catch (RequestException $e) {
            Log::error('AiModelService Error: Invalid request: ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::error('AiModelService Error: unexpected Error by retrieving models: ' . $e->getMessage());
        }

        return [];
    }

    private function getDataFromTimeRange(string $range): array
    {
        $client = app(OpenAiClient::class);
        $service = app(TimeRangeService::class);

        if ($range === 'month') {
            return $this->getDataFromThisMonth($client, $service);
        }

        return $this->getDataFromAllTime($client, $service);
    }

    private function getDataFromThisMonth(OpenAiClient $client, TimeRangeService $service): array
    {
        $cacheKey = 'ai_model_usage_this_month';

        if (! Cache::has($cacheKey)) {
            FetchModelUsageDataJob::dispatch();
            return ['Error' => 'No data found.'];
        }

        $cached = Cache::get($cacheKey);

        // Check if we need to refresh (older than 10 minutes)
        if (isset($cached['time']) && (now()->timestamp - $cached['time'] > 600)) {
            FetchModelUsageDataJob::dispatch();
        }

        // Extract models from cache structure
        $models = $cached['models'] ?? [];

        return $this->getTotalWidgetData([], $models);
    }

    private function getDataFromAllTime(OpenAiClient $client, TimeRangeService $service): array
    {
        $cacheKeyTotal = 'ai_model_usage_total';
        $cacheKeyThisMonth = 'ai_model_usage_this_month';

        if (! Cache::has($cacheKeyTotal) || ! Cache::has($cacheKeyThisMonth)) {
            FetchModelUsageDataJob::dispatch();
            return [];
        }

        // Get both caches
        $totalModels = Cache::get($cacheKeyTotal, []);
        $thisMonthCached = Cache::get($cacheKeyThisMonth, []);
        $thisMonthModels = $thisMonthCached['models'] ?? [];

        // Merge total (all previous months) with this month
        $merged = $totalModels;
        foreach ($thisMonthModels as $modelKey => $data) {
            $merged[$modelKey] = $merged[$modelKey] ?? [
                'requests' => 0,
                'input_tokens' => 0,
                'cached_tokens' => 0,
                'output_tokens' => 0,
            ];

            $merged[$modelKey]['requests'] += $data['requests'] ?? 0;
            $merged[$modelKey]['input_tokens'] += $data['input_tokens'] ?? 0;
            $merged[$modelKey]['cached_tokens'] += $data['cached_tokens'] ?? 0;
            $merged[$modelKey]['output_tokens'] += $data['output_tokens'] ?? 0;
        }

        return $this->getTotalWidgetData([], $merged);
    }

    abstract protected function getTotalWidgetData(array $total, array $models): array;
}
