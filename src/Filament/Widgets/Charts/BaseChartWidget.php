<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts;

use DavidvanSchaik\FilamentAiDashboard\Services\TimeRangeService;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

abstract class BaseChartWidget extends ChartWidget
{
    protected ?string $maxHeight = '400px';

    protected string $view = 'filament-ai-dashboard::filament.widgets.charts.line-chart-widget';

    public ?string $month = null;

    public string $type = 'line';

    public ?string $pollingInterval = null;

    protected $listeners = [
        'monthChanged' => 'onMonthChanged',
    ];

    #[On('MonthChanged')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
    }

    protected function getType(): string
    {
        return $this->type;
    }

    protected function getDaysInMonth(string $month): array
    {
        $daysInMonth = app(TimeRangeService::class)->daysInMonth($month);
        return range(1, $daysInMonth);
    }

    protected function chartColor(int $index): string
    {
        if ($index < 0) {
            return '';
        }

        $colors = [
            '#6366f1',
            '#16a34a',
            '#9333ea',
            '#2563eb',
            '#ef4444',
            '#0ea5e9',
            '#d946ef',
            '#f59e0b',
            '#a855f7',
            '#ec4899',
            '#e11d48',
            '#f97316',
            '#06b6d4',
            '#84cc16',
            '#0891b2',
        ];

        return $colors[$index];
    }

    protected function setDataset(string $color, string $model, array $data): array
    {
        return [
            'label' => $model,
            'data' => array_values($data),
            'borderColor' => $color,
            'fill' => false,
            'lineTension' => 0.3,
        ];
    }
}
