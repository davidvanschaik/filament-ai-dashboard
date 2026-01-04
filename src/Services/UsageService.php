<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use DavidvanSchaik\FilamentAiDashboard\Repositories\MessageRepository;
use Illuminate\Support\Facades\Log;

class UsageService extends OpenAiService
{
    public function __construct(public TokenService $token) {}

    // Returns total tokens and euro for UsageWidget
    public function getTokens(string $range): array
    {
        $data = $this->getUsageData($range);

        if (empty($data)) {
            Log::error('UsageService->getTokens: No data found from API');

            return ['Error' => 'No data found.'];
        }

        return $data;
    }

    protected function getTotalWidgetData(array $total, array $models): array
    {
        foreach ($models as $model => $stats) {
            [$tokens, $euro] = $this->token->countTokens($model, $stats);

            $total['tokens'] = ($total['tokens'] ?? 0) + $tokens;
            $total['euro'] = ($total['euro'] ?? 0) + $euro;
        }

        return $total;
    }

    public function getProjectUsage(string $month): array
    {
        $daysInMonth = app(TimeRangeService::class)->daysInMonth($month);

        $start = "{$month}-1 00:00:00";
        $end = "{$month}-{$daysInMonth} 23:59:59";

        return app(MessageRepository::class)->getMessagesFromMonth($start, $end);
    }

    // Builds array with all project data for the project overview tables at the bottom of the page.
    public function getProjectStatistics(string $month): array
    {
        $projectData = $this->getProjectUsage($month);
        $totalData = [];

        foreach ($projectData as $data) {
            $tokens = [
                'input_tokens' => $data['input_tokens'] - $data['cached_tokens'],
                'cached_tokens' => $data['cached_tokens'],
                'output_tokens' => $data['output_tokens'],
            ];

            $project = $data['project'];

            if (!isset($totalData[$project])) {
                $totalData[$project] = [
                    'project' => $project,
                    'total_tokens' => 0,
                    'total_euro' => 0,
                ];
            }

            $totalData[$project]['total_tokens'] += $data['input_tokens'] + $data['output_tokens'];
            $totalData[$project]['total_euro'] += $this->token->convertTokensToEuros($tokens, $data['model']);
        }

        foreach ($totalData as $project => $data) {
            $totalData[$project]['total_euro'] = round($data['total_euro'], 2);
        }

        return $totalData;
    }

    public function sortProjectDailyUsage(string $month): array
    {
        $results = $this->getProjectUsage($month);
        $projectDailyUsage = [];

        foreach ($results as $result) {
            $project = $result['project'];
            $day = ltrim($result['day'], '0');
            $model = $result['model'];

            if (!isset($projectDailyUsage[$project][$day][$model])) {
                $projectDailyUsage[$project][$day][$model] = [
                    'input_tokens' => 0,
                    'cached_tokens' => 0,
                    'output_tokens' => 0,
                ];
            }

            $projectDailyUsage[$project][$day][$model]['input_tokens'] += $result['input_tokens'];
            $projectDailyUsage[$project][$day][$model]['cached_tokens'] += $result['cached_tokens'];
            $projectDailyUsage[$project][$day][$model]['output_tokens'] += $result['output_tokens'];
        }

        return $projectDailyUsage;
    }
}
