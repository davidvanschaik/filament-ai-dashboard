<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Pages;

use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\JobsWidget;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\ModelsWidget;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\StorageWidget;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\UsageWidget;
use Filament\Pages\Page;
use UnitEnum;

class AiMonitoringDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected string $view = 'filament-ai-dashboard::filament.pages.ai-dashboard';
    protected static ?string $title = 'AI Monitoring';
    protected static ?string $slug = 'monitoring';
    public static string | UnitEnum | null $navigationGroup = null;

    protected function getHeaderWidgets(): array
    {
        return config('filament-ai-dashboard.widgets', [
            ModelsWidget::class,
            UsageWidget::class,
            StorageWidget::class,
            JobsWidget::class,
        ]);
    }
}
