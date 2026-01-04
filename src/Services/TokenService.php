<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use DavidvanSchaik\FilamentAiDashboard\Repositories\MessageRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenService
{
    public function getModelTokens(array $data): array
    {
        $models = [];

        if (! isset($data['data'])) {
            Log::error('UsageService->getModelTokens: No data found from API');

            return [];
        }

        foreach ($data['data'] as $bucket) {
            $this->sortModelTokensPerDay($bucket, $models);
        }

        return $models;
    }

    public function countTokens(string $model, array $stats): array
    {
        $pricing = $this->getModelPricing($model);
        $tokens = 0;
        $dollar = 0;

        foreach ($stats as $key => $stat) {
            if ($key === 'requests') {
                continue;
            }

            $tokens = $tokens + $stat;
            $dollar = $dollar + ($stat / 1000000 * $pricing[$key]);
        }
        $euro = $this->dollarToEuro($dollar);

        return [$tokens, $euro];
    }

    // Get model pricing from config, fallback to default.
    private function getModelPricing(string $modelName): array
    {
        $name = $this->normalizeModelName($modelName);
        $pricing = config("filament-ai-dashboard-pricing");

        if (array_key_exists($name, $pricing)) {
            return $pricing[$name];
        }

        foreach (array_keys(config('filament-ai-dashboard-pricing')) as $key) {
            if (str_starts_with($name, $key) || str_ends_with($key, $name)) {

                return $pricing[$key];
            }
        }
        Log::warning("No pricing found for model: {$modelName} (normalized: {$name})");

        return config('filament-ai-dashboard-pricing.default');
    }

    // Remove dates and suffixes from model name.
    private function normalizeModelName(string $modelName): string
    {
        $modelName = preg_replace('/-\d{4}-\d{2}-\d{2}$/', '', $modelName);
        $modelName = preg_replace('/-(tts|transcribe.*|diarize)$/', '', $modelName);
        $modelName = rtrim($modelName, '-');

        return strtolower($modelName);
    }

    private function sortModelTokensPerDay(array $bucket, array &$models): void
    {
        $service = app(TimeRangeService::class);
        $time = $service->parseToDate($bucket['start_time']);

        foreach ($bucket['results'] as $result) {;
            $models[$result['model']][$time] = [
                'input_tokens' => $result['input_tokens'] - $result['input_cached_tokens'],
                'cached_tokens' => $result['input_cached_tokens'],
                'output_tokens' => $result['output_tokens'],
            ];
        }
    }

    public function getEuros(array $result): float
    {
        $tokens = [
            'input_tokens' => $result['input_tokens'] - $result['input_cached_tokens'],
            'cached_tokens' => $result['input_cached_tokens'],
            'output_tokens' => $result['output_tokens'],
        ];

        return $this->convertTokensToEuros($tokens, $result['model']);
    }

    public function convertTokensToEuros(array $tokens, string $model): float
    {
        $pricing = $this->getModelPricing($model);
        $dollar = 0;

        foreach ($tokens as $key => $token) {
            $dollar += ($token / 1000000 * $pricing[$key]);
        }

        return round($this->dollarToEuro($dollar), 2);
    }

    private function dollarToEuro(float $dollar): float
    {
        $rate = cache()->remember('dollar_rate', 3600, function () {
            $response = Http::timeout(5)->get('https://api.frankfurter.app/latest?from=USD&to=EUR');
            return $response->json()['rates']["EUR"];
        });

        return $rate * $dollar;
    }

    public function countProjectEuros(array $day): float
    {
        $euro = 0;
        foreach ($day as $modelName => $tokens) {
            $euro += $this->convertTokensToEuros($tokens, $modelName);
        }

        return $euro;
    }

    public function countProjectTokens(array $day): int
    {
        $totalTokens = 0;
        foreach ($day as $modelName => $tokens) {
            $totalTokens += $tokens['input_tokens'];
            $totalTokens += $tokens['output_tokens'];
        }

        return $totalTokens;
    }

    public function countJobEuroTokens(array $tokens): array
    {
        $totalTokens = 0;
        $totalEuro = 0;

        foreach ($tokens as $modelName => $token) {
            $totalTokens += $token['input_tokens'] + $token['output_tokens'];
            $totalEuro += $this->convertTokensToEuros($token, $modelName);
        }

        return [$totalTokens, $totalEuro];
    }

    public function getJobTokensByMessageId(array $messageIds): array
    {
        $tokens = [];
        $repository = app(MessageRepository::class);
        $messages = $repository->findMany($messageIds);

        foreach ($messages as $message) {
            $metadata = json_decode($message->metadata, true);
            $model = $metadata['model'];

            if (! isset($tokens[$model])) {
                $tokens[$model] = [
                    'input_tokens' => 0,
                    'cached_tokens' => 0,
                    'output_tokens' => 0,
                ];
            }

            $tokens[$model]['input_tokens'] += $message->input_tokens;
            $tokens[$model]['cached_tokens'] += $message->input_cached_tokens;
            $tokens[$model]['output_tokens'] += $message->output_tokens;
        }

        return $tokens;
    }
}
