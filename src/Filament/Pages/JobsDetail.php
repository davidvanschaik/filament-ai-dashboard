<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\BaseDetailPage;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsDurationChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsExecutedChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\JobsOverviewTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class JobsDetail extends BaseDetailPage
{
    protected string $view = 'filament-ai-dashboard::filament.pages.jobs-detail';
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
