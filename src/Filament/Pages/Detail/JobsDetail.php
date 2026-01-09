<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail;

use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsDurationChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsExecutedChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\JobsOverviewTable;

class JobsDetail extends BaseDetailPage
{
    protected string $view = 'filament-ai-dashboard::filament.pages.detail.jobs-detail';
    protected ?string $heading = 'Jobs Detail';

    public function getHeaderWidgets(): array
    {
        return [
            JobsTokenEuroChart::class,
            JobsExecutedChart::class,
            JobsDurationChart::class,
            JobsOverviewTable::class,
        ];
    }
}
