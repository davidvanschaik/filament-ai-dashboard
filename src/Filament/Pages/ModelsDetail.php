<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsRequestChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelOverviewTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ModelsDetail extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.pages.models-detail';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = ['month' => null];

    protected function getHeaderWidgets(): array
    {
        return [
            ModelsTokenEuroChart::class,
            ModelsRequestChart::class,
            ModelOverviewTable::class,
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
            'heading' => 'Models detail'
        ]);
    }
}
