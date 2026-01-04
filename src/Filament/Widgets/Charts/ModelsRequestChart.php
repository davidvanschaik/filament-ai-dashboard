<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;

class ModelsRequestChart extends BaseChartWidget
{
    protected ?string $heading = 'Models Requests';

    protected function getData(): array
    {
        $month = $this->month ?? now()->format('Y-m');

        $service = app(AiModelService::class);
        $requests = $service->getModelRequests($month);
        $labels = $this->getDaysInMonth($month);

        $datasets = $this->createDatasetForChart($requests, end($labels));

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function createDatasetForChart(array $requests, int $daysInMonth): array
    {
        $colorIndex = 0;
        $datasets = [];

        foreach ($requests as $model => $daysCount) {
            $data = array_fill(1, $daysInMonth, 0);

            foreach ($daysCount as $day => $count) {
                $data[ltrim($day, '0')] = $count;
            }
            $color = $this->chartColor($colorIndex);
            $colorIndex++;

            $datasets[] = $this->setDataset($color, $model, $data);
        }

        return $datasets;
    }
}
