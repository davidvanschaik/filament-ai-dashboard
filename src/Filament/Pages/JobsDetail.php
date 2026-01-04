<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsDurationChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsExecutedChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\JobsOverviewTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class JobsDetail extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.pages.jobs-detail';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = ['month' => null];

    public function getHeaderWidgets(): array
    {
        return [
            JobsTokenEuroChart::class,
            JobsExecutedChart::class,
            JobsDurationChart::class,
            JobsOverviewTable::class,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::monthPicker()
                    ->afterStateUpdated(fn ($state) => $this->updateDataMonth($state))
            ])
            ->statePath('data');
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getWidgetData(): array
    {
        return [
            'month' => $this->data['month']
        ];
    }

    public function updateDataMonth(string $value): void
    {
        if ($value) {
            $this->dispatch('monthChanged', $value);
        }
    }

    public function getHeader(): ?View
    {
        return view('filament-ai-dashboard::filament.pages.detail.header', [
            'heading' => 'Jobs Detail'
        ]);
    }
}
