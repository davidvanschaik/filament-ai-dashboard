<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\BaseDetailPage;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ProjectTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelUsageTable;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ProjectUsageTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class UsageDetail extends BaseDetailPage
{
    protected string $view = 'filament-ai-dashboard::filament.pages.usage-detail';
    protected ?string $heading = 'Usage Detail';

    protected function getHeaderWidgets(): array
    {
        return [
            ModelsTokenEuroChart::class,
            ModelUsageTable::class,
            ProjectTokenEuroChart::class,
            ProjectUsageTable::class,
        ];
    }
}
