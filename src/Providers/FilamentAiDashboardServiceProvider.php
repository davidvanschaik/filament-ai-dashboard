<?php

namespace DavidvanSchaik\FilamentAiDashboard\Providers;


use DavidvanSchaik\FilamentAiDashboard\Console\InstallFilamentAiDashboardCommand;
use DavidvanSchaik\FilamentAiDashboard\Console\PublishEnvVariablesCommand;
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
        foreach (config('filament-ai-dashboard-providers', []) as $provider) {
            $this->app->register($provider);
        }
    }
}
