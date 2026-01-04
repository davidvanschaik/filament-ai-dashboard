<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Services\JobService;

class JobsExecutedChart extends BaseChartWidget
{
    protected ?string $heading = 'Jobs Executed Chart';

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
        $data = array_column($jobs, 'execution_count');
        $colors = array_map(
            fn ($i) => $this->chartColor($i),
            range(0, count($labels) - 1)
        );

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Executions',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ]
            ]
        ];
    }
}
