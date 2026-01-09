<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail;

use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsRequestChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelOverviewTable;

class ModelsDetail extends BaseDetailPage
{
    protected string $view = 'filament-ai-dashboard::filament.pages.detail.models-detail';
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
