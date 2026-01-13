<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use DavidvanSchaik\FilamentAiDashboard\Services\TokenService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class ModelsTokenEuroChart extends BaseChartWidget implements HasForms
{
    use InteractsWithForms;

    protected ?string $heading = 'Models Usage';
    public ?array $data = ['convert' => false];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getData(): array
    {
        $month = $this->month ?? now()->format('Y-m');

        $service = app(AiModelService::class);
        $tokens  = $service->getModelTokens($month);
        $labels = $this->getDaysInMonth($month);

        $datasets = $this->createDatasetForChart($tokens, end($labels));

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function createDatasetForChart(array $tokens, int $daysInMonth): array
    {
        $colorIndex = 0;
        $datasets = [];

        foreach ($tokens as $model => $daysCount) {
            $totalData = array_fill(1, $daysInMonth, 0);

            foreach ($daysCount as $day => $count) {
                $data = $count['input_tokens'] + $count['output_tokens'];

                if ($this->data['convert']) {
                    $data = app(TokenService::class)->convertTokensToEuros($count, $model);
                }
                $totalData[ltrim($day, '0')] = $data;
            }
            $color = $this->chartColor($colorIndex);
            $colorIndex++;

            $datasets[] = $this->setDataset($color, $model, $totalData);
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
