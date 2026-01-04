<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAIClient;
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
        [$start, $end] = $service->getThisMonthTimestamps();
        $models = $this->fetchAndFilter($client, $start, $end);

        $total = [];

        return $this->getTotalWidgetData($total, $models);
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

    private function getDataFromAllTime(OpenAiClient $client, TimeRangeService $service): array
    {
        $now = Carbon::now('UTC');
        $monthTime = $now->copy()->startOfMonth();

        $thisMonth = true;
        $total = [];

        while (true) {
            [$start, $end] = $thisMonth
                ? $service->getThisMonthTimestamps()
                : $service->getMonthTimeStamps($monthTime);

            $models = $this->cacheModels($thisMonth, $monthTime, $client, $start, $end);

            if (count($models) === 0) {
                break;
            }

            $total = $this->getTotalWidgetData($total, $models);

            $thisMonth = false;
            $monthTime->subMonthNoOverflow()->startOfMonth();
        }

        return $total;
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

    private function cacheModels(bool $thisMonth, Carbon $time, OpenAiClient $client, int $start, int $end): array
    {
        $cacheKey = 'ai_model_' . $time->format('Y-m');

        if (! $thisMonth && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $models = $this->fetchAndFilter($client, $start, $end);

        if (! $thisMonth && ! empty($models)) {
            Cache::forever($cacheKey, $models);
        }

        return $models;
    }

    abstract protected function getTotalWidgetData(array $total, array $models): array;
}
