<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

abstract class BaseDashboardWidget extends Widget
{
    protected array $widgetData = [];
    protected ?string $errorMessage = null;

    public function mount(): void
    {
        $this->loadWidgetData();
    }

    abstract protected function loadWidgetData(): void;
}
