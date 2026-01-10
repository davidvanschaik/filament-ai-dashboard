<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard;

use DavidvanSchaik\FilamentAiDashboard\Services\StorageService;

class StorageWidget extends BaseDashboardWidget
{
    protected string $view = 'filament-ai-dashboard::filament.widgets.dashboard.storage-widget';
    protected ?string $heading = "Stored data";

    protected function loadWidgetData(): void
    {
        $service = app(StorageService::class);
        $result = $service->getStorageData();

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->widgetData = $result;
        }
    }
}
