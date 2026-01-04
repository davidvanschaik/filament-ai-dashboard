<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Services\JobService;

class JobsDurationChart extends BaseChartWidget
{
    protected ?string $heading = 'Jobs Duration Chart';

    protected function getData(): array
    {
        $this->type = 'bar';

        $month = $this->month ?? now()->format('Y-m');
        $jobs = app(JobService::class)->getExecutedJobsFromTimeRange($month);

        return $this->createDatasetForChart($jobs);
    }

    protected function createDatasetForChart(array $jobs): array
    {
        $labels = array_keys($jobs);
        $colors = array_map(fn ($i) => $this->chartColor($i), range(0, count($labels) - 1));
        $data = array_map(function ($job) {
            $average_duration = $job['execution_count'] > 0 ? $job['total_duration'] / $job['execution_count'] : 0;

            return round($average_duration / 60, 2);
        }, $jobs);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Duration in minutes',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ]
            ]
        ];
    }
}
