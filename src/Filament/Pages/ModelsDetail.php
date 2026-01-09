<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\BaseDetailPage;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsRequestChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelOverviewTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ModelsDetail extends BaseDetailPage
{
    protected string $view = 'filament-ai-dashboard::filament.pages.models-detail';
    protected ?string $heading = 'Models Detail';

    protected function getHeaderWidgets(): array
    {
        return [
            ModelsTokenEuroChart::class,
            ModelsRequestChart::class,
            ModelOverviewTable::class,
        ];
    }
}
