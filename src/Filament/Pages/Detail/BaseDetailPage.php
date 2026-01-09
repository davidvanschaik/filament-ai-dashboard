<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

abstract class BaseDetailPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public ?array $data = ['month' => null];

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
            'heading' => $this->heading
        ]);
    }
}
