<?php

namespace DavidvanSchaik\FilamentAiDashboard;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAiDashboardPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-ai-dashboard';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            \DavidvanSchaik\FilamentAiDashboard\Filament\Pages\AiMonitoringDashboard::class,
            \DavidvanSchaik\FilamentAiDashboard\Filament\Pages\ModelsDetail::class,
            \DavidvanSchaik\FilamentAiDashboard\Filament\Pages\UsageDetail::class,
            \DavidvanSchaik\FilamentAiDashboard\Filament\Pages\JobsDetail::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
