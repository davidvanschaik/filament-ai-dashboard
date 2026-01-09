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
            Filament\Pages\Detail\ModelsDetail::class,
            Filament\Pages\Detail\UsageDetail::class,
            Filament\Pages\Detail\JobsDetail::class,
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
