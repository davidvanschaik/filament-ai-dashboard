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

        $models = Cache::get($cacheKey);

        if (now()->timestamp - $models['time'] > 600) {
            FetchModelUsageDataJob::dispatch();
        }

        return $this->getTotalWidgetData([], $models);
    }

    private function getDataFromAllTime(OpenAiClient $client, TimeRangeService $service): array
    {
        $cacheKeyTotal = 'ai_model_usage_total';
        $cacheKey = 'ai_model_usage_this_month';
        $models = [];

        if (! Cache::has($cacheKey) || ! Cache::has($cacheKeyTotal)) {
            FetchModelUsageDataJob::dispatch();
            return ['Error' => 'No data found.'];
        }

        $models[] = Cache::get($cacheKey);
        $models[] = Cache::get($cacheKeyTotal);

        return $this->getTotalWidgetData([], $models);
    }

    abstract protected function getTotalWidgetData(array $total, array $models): array;
}
