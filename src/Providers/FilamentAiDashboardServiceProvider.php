<?php

namespace DavidvanSchaik\FilamentAiDashboard\Providers;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Console\CreateFilamentThemeFileCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\InstallFilamentAiDashboardCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\PublishEnvVariablesCommand;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\AiMonitoringDashboard;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets;

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
    }

    public function packageRegistered(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/filament-ai-dashboard.php', 'filament-ai-dashboard');
        AiMonitoringDashboard::$navigationGroup = config('filament-ai-dashboard.navigation_group');

        foreach (config('filament-ai-dashboard-providers', []) as $class) {
            $this->app->singleton($class);
        }
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__ . '/../../data' => storage_path('app/filament-ai-dashboard/data'),
        ], 'filament-ai-dashboard-data');

        if (class_exists(Filament::class)) {
            Filament::registerPages([
                AiMonitoringDashboard::class
            ]);
        }

        $widgets = [
            Widgets\ModelsWidget::class => 'models-widget',
            Widgets\UsageWidget::class => 'usage-widget',
            Widgets\StorageWidget::class => 'storage-widget',
            Widgets\JobsWidget::class => 'jobs-widget',
            Widgets\Charts\ModelsRequestChart::class => 'charts.models-request-chart',
            Widgets\Charts\ModelsTokenEuroChart::class => 'charts.models-token-euro-chart',
            Widgets\Charts\ProjectTokenEuroChart::class => 'charts.project-token-euro-chart',
            Widgets\Charts\JobsExecutedChart::class => 'charts.jobs-executed-chart',
            Widgets\Charts\JobsDurationChart::class => 'charts.jobs-duration-chart',
            Widgets\Charts\JobsTokenEuroChart::class => 'charts.jobs-token-euro-chart',
            Widgets\Tables\ModelOverviewTable::class => 'tables.model-overview-table',
            Widgets\Tables\ModelUsageTable::class => 'tables.model-usage-table',
            Widgets\Tables\JobsOverviewTable::class => 'tables.jobs-overview-table',
            Widgets\Tables\ProjectUsageTable::class => 'tables.project-usage-table',
        ];

        foreach ($widgets as $class => $widget) {
            Livewire::component(
                "davidvan-schaik.filament-ai-dashboard.filament.widgets.{$widget}",
                $class
            );
        }
    }
}
