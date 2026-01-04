<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use DavidvanSchaik\FilamentAiDashboard\Repositories\TaskRunRepository;

class JobService
{
    public function __construct(public TokenService $token) {}

    public function getExecutedJobs(int $limit): array
    {
        $jobs = app(TaskRunRepository::class)->getExecutedJobs();

        if (empty($jobs)) {
            return ['Error' => 'No jobs found.'];
        }

        return app(FilterService::class)->sortData($jobs, $limit);
    }

    public function getExecutedJobsFromTimeRange(string $month): array
    {
        $daysInMonth = app(TimeRangeService::class)->daysInMonth($month);

        $start = "{$month}-01 00:00:00";
        $end = "{$month}-{$daysInMonth} 23:59:59";

        return app(TaskRunRepository::class)->getExecutedJobs($start, $end);
    }

    public function getJobUsedTokens(string $month): array
    {
        $jobs = $this->getExecutedJobsFromTimeRange($month);

        foreach ($jobs as &$job) {
            $job['tokens'] = $this->token->getJobTokensByMessageId($job['message_ids']);
        }

        return $jobs;
    }

    // Builds array with all job data for the job overview tables at the bottom of the page.
    public function getJobsStatistics(string $month): array
    {
        $statistics = [];
        $jobs = $this->getJobUsedTokens($month);

        foreach ($jobs as $jobName => $job) {
            [$totalTokens, $totalEuro] = $this->token->countJobEuroTokens($job['tokens']);

            $statistics[$jobName] = [
                'job' => $jobName,
                'execution_count' => $job['execution_count'],
                'total_tokens' => $totalTokens,
                'total_euro' => "€ " . number_format($totalEuro, 2, '.', ''),
                'average_duration' => round(($job['total_duration'] / $job['execution_count']) / 60)
            ];
        }

        return $statistics;
    }
}
