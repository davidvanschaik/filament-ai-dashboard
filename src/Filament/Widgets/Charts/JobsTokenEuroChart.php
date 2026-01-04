<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Services\JobService;
use DavidvanSchaik\FilamentAiDashboard\Services\TokenService;
use Filament\Schemas\Schema;

class JobsTokenEuroChart extends BaseChartWidget
{
    protected ?string $heading = 'Jobs Token Euro Chart';

    public ?array $data = ['convert' => false];

    protected function getData(): array
    {
        $this->type = 'bar';

        $month = $this->month ?? now()->format('Y-m');
        $jobs = app(JobService::class)->getJobUsedTokens($month);

        return $this->createDatasetForChart($jobs);
    }

    protected function createDatasetForChart(array $jobs): array
    {
        $labels = array_keys($jobs);
        $colors = array_map(fn ($i) => $this->chartColor($i), range(0, count($labels) - 1));
        $data = array_map(function ($job) {
            $total = 0;

            foreach ($job['tokens'] as $model => $token) {
                $total += $this->data['convert']
                    ? app(TokenService::class)->convertTokensToEuros($token, $model)
                    : $token['input_tokens'] + $token['output_tokens'];
            }

            return $total;
        }, $jobs);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->data['convert'] ? 'Jobs Total Euro' : 'Jobs Total Tokens',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ]
            ]
        ];
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
