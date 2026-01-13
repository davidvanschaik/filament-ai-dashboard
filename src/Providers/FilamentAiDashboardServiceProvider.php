<?php

namespace DavidvanSchaik\FilamentAiDashboard\Providers;

use DavidvanSchaik\FilamentAiDashboard\Console\CreateFilamentThemeFileCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\InstallFilamentAiDashboardCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\PublishEnvVariablesCommand;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages;
use DavidvanSchaik\FilamentAiDashboard\Filament;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAiDashboardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-ai-dashboard')
            ->hasRoutes(['api'])
            ->hasConfigFile([
                'filament-ai-dashboard-api',
                'filament-ai-dashboard-pricing',
                'filament-ai-dashboard-providers',
                'filament-ai-dashboard',
            ])
            ->hasMigrations([
                'create_tasks_table',
                'create_projects_table',
                'create_messages_table',
                'create_task_runs_table',
            ])
            ->hasViews()
            ->hasCommands([
                CreateFilamentThemeFileCommand::class,
                InstallFilamentAiDashboardCommand::class,
                PublishEnvVariablesCommand::class,
            ]);
    }

    public function boot(): void
    {
        parent::boot();

        Route::prefix('api')
            ->middleware(['api'])
            ->group(__DIR__ . '/../../routes/api.php');
    }

    public function packageRegistered(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/filament-ai-dashboard.php', 'filament-ai-dashboard');

        foreach (config('filament-ai-dashboard-providers', []) as $class) {
            $this->app->singleton($class);
        }
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__ . '/../../data' => storage_path('app/filament-ai-dashboard/data'),
        ], 'filament-ai-dashboard-data');

        Pages\AiMonitoringDashboard::$navigationGroup = config('filament-ai-dashboard.navigation_group');

        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::registerPages([
                Pages\AiMonitoringDashboard::class,
                Pages\Detail\JobsDetail::class,
                Pages\Detail\ModelsDetail::class,
                Pages\Detail\UsageDetail::class,
            ]);
        }

        $widgets = [
            Filament\Pages\Detail\ModelsDetail::class               => 'pages.detail.models-detail',
            Filament\Pages\Detail\UsageDetail::class                => 'pages.detail.usage-detail',
            Filament\Pages\Detail\JobsDetail::class                 => 'pages.detail.jobs-detail',
            Filament\Widgets\ModelsWidget::class                    => 'widgets.models-widget',
            Filament\Widgets\UsageWidget::class                     => 'widgets.usage-widget',
            Filament\Widgets\StorageWidget::class                   => 'widgets.storage-widget',
            Filament\Widgets\JobsWidget::class                      => 'widgets.jobs-widget',
            Filament\Widgets\Charts\ModelsRequestChart::class       => 'widgets.charts.models-request-chart',
            Filament\Widgets\Charts\ModelsTokenEuroChart::class     => 'widgets.charts.models-token-euro-chart',
            Filament\Widgets\Charts\ProjectTokenEuroChart::class    => 'widgets.charts.project-token-euro-chart',
            Filament\Widgets\Charts\JobsExecutedChart::class        => 'widgets.charts.jobs-executed-chart',
            Filament\Widgets\Charts\JobsDurationChart::class        => 'widgets.charts.jobs-duration-chart',
            Filament\Widgets\Charts\JobsTokenEuroChart::class       => 'widgets.charts.jobs-token-euro-chart',
            Filament\Widgets\Tables\ModelOverviewTable::class       => 'widgets.tables.model-overview-table',
            Filament\Widgets\Tables\ModelUsageTable::class          => 'widgets.tables.model-usage-table',
            Filament\Widgets\Tables\JobsOverviewTable::class        => 'widgets.tables.jobs-overview-table',
            Filament\Widgets\Tables\ProjectUsageTable::class        => 'widgets.tables.project-usage-table',
        ];

        foreach ($widgets as $class => $widget) {
            Livewire::component(
                "davidvan-schaik.filament-ai-dashboard.filament.$widget",
                $class
            );
        }
    }
}
