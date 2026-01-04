<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use Illuminate\Support\Facades\Log;

class AiModelService extends OpenAiService
{
    public function __construct(public TokenService $token) {}

    public function getMostUsedModels(int $limit, string $range): array
    {
        $data = $this->getUsageData($range);
        $filter = app(FilterService::class);

        if (empty($data)) {
            Log::error('AiModelService->getMostUsedModels: No data found from API');

            return ['Error' => 'No data found.'];
        }

        return $filter->sortData($data, $limit);
    }

    protected function getTotalWidgetData(array $total, array $models): array
    {
        foreach ($models as $model => $stats) {
            $modelName = $this->sortModelsByFamily($model);
            $total[$modelName] = ($total[$modelName] ?? 0) + $stats['requests'];
        }

        return $total;
    }

    private function sortModelsByFamily(string $model): string
    {
        if (preg_match('/gpt-(\d+)/', $model, $matches)) {
            return "GPT {$matches[1]}";
        }

        if (preg_match('/^o\d/', $model, $matches)) {
            return "Omni";
        }

        return 'Unknown';
    }

    public function getModelRequests(string $month): array
    {
        $data = $this->getRawModels($month);
        $models = [];

        if (! isset($data['data'])) {
            Log::error('AiModelService->getModelRequests: No data found from API');

            return [];
        }

        foreach ($data['data'] as $bucket) {
            $this->SortDailyRequestActivity($bucket, $models);
        }

        return $models;
    }

    private function SortDailyRequestActivity(array $bucket, array &$models): void
    {
        $service = app(TimeRangeService::class);
        $time = $service->parseToDate($bucket['start_time']);

        foreach ($bucket['results'] as $result) {
            $models[$result['model']][$time] = $result['num_model_requests'];
        }
    }

    public function getModelTokens(string $month): array
    {
        $data = $this->getRawModels($month);

        return $this->token->getModelTokens($data);
    }

     // Builds array with all model data for the model overview tables at the bottom of the page.
    public function getModelStatistics(string $month): array
    {
        $data = $this->getRawModels($month);
        $models = [];

        if (! isset($data['data'])) {
            Log::error('AiModelService->getModelStatistics: No data found from API');

            return [];
        }

        foreach ($data['data'] as $bucket) {
            $this->sortTotalModelData($bucket, $models);
        }

        return $this->calculateAvgPerRequest($models);
    }

    // Adds all the model requests, total tokens, total euro's and calculates average tokens/euro's per requests
    private function sortTotalModelData(array $bucket, array &$models): void
    {
        foreach ($bucket['results'] as $result) {
            $model = $result['model'];

            $models[$model]['model'] = $model;
            $models[$model]['requests'] = ($models[$model]['requests'] ?? 0 ) + $result['num_model_requests'];
            $models[$model]['total_tokens'] = ($models[$model]['total_tokens'] ?? 0) +
                ($result['input_tokens'] - $result['input_cached_tokens']) +  $result['output_tokens'];
            $models[$model]['total_euro'] =  ($models[$model]['total_euro'] ?? 0) + $this->token->getEuros($result);
        }
    }

    private function calculateAvgPerRequest(array $models): array
    {
        foreach ($models as $model => $stats) {
            $models[$model]['average_token_per_request'] = round($stats['total_tokens'] / $stats['requests']);
            $models[$model]['average_euro_per_request'] = round($stats['total_euro'] / $stats['requests'], 2);
        }

        return $models;
    }
}
