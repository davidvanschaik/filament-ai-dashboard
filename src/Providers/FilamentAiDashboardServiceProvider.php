<?php

namespace DavidvanSchaik\FilamentAiDashboard\Providers;


use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Console\CreateFilamentThemeFileCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\InstallFilamentAiDashboardCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\PublishEnvVariablesCommand;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\AiDashboard;
use Filament\Facades\Filament;
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
                'filament-ai-dashboard-widgets',
            ])
            ->hasMigrations([
                'create_tasks_table',
                'create_projects_table',
                'create_messages_table',
                'create_task_runs_table',
            ])
            ->runsMigrations()
            ->hasViews()
            ->hasCommands([
                CreateFilamentThemeFileCommand::class,
                InstallFilamentAiDashboardCommand::class,
                PublishEnvVariablesCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        foreach (config('filament-ai-dashboard-providers', []) as $class) {
            $this->app->singleton($class);
        }
    }

    public function packageBooted(): void
    {
        $this->publishMigrations();

        $this->publishes([
            __DIR__ . '/../../data' => storage_path('app/filament-ai-dashboard/data'),
        ], 'filament-ai-dashboard-data');

        if (class_exists(Filament::class)) {
            Filament::registerPages([
                AiDashboard::class
            ]);
        }

        $widgets = [
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\ModelsWidget::class => 'models-widget',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\UsageWidget::class => 'usage-widget',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\StorageWidget::class => 'storage-widget',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\JobsWidget::class => 'jobs-widget',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsRequestChart::class => 'charts.models-request-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart::class => 'charts.models-token-euro-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ProjectTokenEuroChart::class => 'charts.project-token-euro-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsExecutedChart::class => 'charts.jobs-executed-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsDurationChart::class => 'charts.jobs-duration-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsTokenEuroChart::class => 'charts.jobs-token-euro-chart',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelOverviewTable::class => 'tables.model-overview-table',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ModelUsageTable::class => 'tables.model-usage-table',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\JobsOverviewTable::class => 'tables.jobs-overview-table',
            \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ProjectUsageTable::class => 'tables.project-usage-table',
        ];

        foreach ($widgets as $class => $widget) {
            Livewire::component(
                "davidvan-schaik.filament-ai-dashboard.filament.widgets.{$widget}",
                $class
            );
        }
    }

    private function publishMigrations(): void
    {
        $migrations = __DIR__ . '/../../database/migrations';
        $stubs = glob($migrations . '/*.php.stub');

        $now = Carbon::now();

        foreach ($stubs as $index => $stub) {
            $migrationName = basename($stub, '.php.stub');
            $timestamp = $now->copy()->addSecond($index)->format('Y_m_d_His');
            $target = database_path("migrations/{$timestamp}_{$migrationName}.php");

            if (! file_exists($target)) {
                $this->publishes([
                    $stub => $target,
                ], 'filament-ai-dashboard-migrations');
            }
        }
    }
}
