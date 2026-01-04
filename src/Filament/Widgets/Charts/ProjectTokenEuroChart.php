<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Services\TokenService;
use DavidvanSchaik\FilamentAiDashboard\Services\UsageService;
use Filament\Schemas\Schema;

class ProjectTokenEuroChart extends BaseChartWidget
{
    protected ?string $heading = 'Project Usage';

    public ?array $data = ['convert' => false];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getData(): array
    {
        $month = $this->month ?? now()->format('Y-m');
        $service = app(UsageService::class);

        $labels = $this->getDaysInMonth($month);
        $projectDailyUsage = $service->sortProjectDailyUsage($month);

        $dataset = $this->createDatasetForChart($projectDailyUsage, end($labels));

        return [
            'labels' => $labels,
            'datasets' => $dataset,
        ];
    }

    protected function createDatasetForChart(array $projectDailyUsage, int $daysInMonth): array
    {
        $colorIndex = 0;
        $datasets = [];
        $service = app(TokenService::class);

        foreach ($projectDailyUsage as $projectName => $project) {
            $totalData = array_fill(1, $daysInMonth, 0);

            foreach ($project as $day => $models) {
                $totalData[$day] = $this->data['convert']
                    ? $service->countProjectEuros($models)
                    : $service->countProjectTokens($models);
            }
            $color = $this->chartColor($colorIndex);
            $colorIndex++;

            $datasets[] = $this->setDataset($color, $projectName, $totalData);
        }

        return $datasets;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::convertToggle()->afterStateUpdated(fn () => $this->updateChartData())
            ])
            ->statePath('data');
    }
}
