<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets;

use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\JobsDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\JobService;
use Filament\Widgets\Widget;

class JobsWidget extends Widget
{
    protected string $view = 'filament-ai-dashboard::filament.widgets.jobs-widget';

    protected ?string $heading = "Executed Jobs";

    public array $jobs = [];

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->loadJobs();
    }

    public function loadJobs(): void
    {
        $service = app(JobService::class);
        $result = $service->getExecutedJobs(3);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->jobs = $result;
        }
    }

    public function openDetails(): void
    {
        $this->redirect(JobsDetail::getUrl());
    }
}
