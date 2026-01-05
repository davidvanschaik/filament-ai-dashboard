<?php

namespace DavidvanSchaik\FilamentAiDashboard\Providers;


use DavidvanSchaik\FilamentAiDashboard\Console\InstallFilamentAiDashboardCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\PublishEnvVariablesCommand;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\AiDashboard;
use DavidvanSchaik\FilamentAiDashboard\FilamentAiDashboardPlugin;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
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
            ->hasViews()
            ->hasCommands([
                InstallFilamentAiDashboardCommand::class,
                PublishEnvVariablesCommand::class
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
        if (class_exists(Filament::class)) {
            Filament::registerPages([
                AiDashboard::class
            ]);
        }
    }
}
