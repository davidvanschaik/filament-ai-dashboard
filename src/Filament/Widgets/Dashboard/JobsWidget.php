<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard;

use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\JobsDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\JobService;

class JobsWidget extends BaseDashboardWidget
{
    protected string $view = 'filament-ai-dashboard::filament.widgets.dashboard.jobs-widget';
    protected ?string $heading = "Executed Jobs";

    protected function loadWidgetData(): void
    {
        $service = app(JobService::class);
        $result = $service->getExecutedJobs(3);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->widgetData = $result;
        }
    }

    public function openDetails(): void
    {
        $this->redirect(JobsDetail::getUrl());
    }
}
