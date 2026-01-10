<?php

/**
 * Here can you change the order of the widgets on the dashboard.
 */
return [
    'widgets' => [
        \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard\ModelsWidget::class,
        \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard\UsageWidget::class,
        \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard\StorageWidget::class,
        \DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard\JobsWidget::class,
    ],
    'navigation_group' => 'System management',
];
