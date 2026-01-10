<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\UsageDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\UsageService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class UsageWidget extends BaseDashboardWidget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.widgets.dashboard.usage-widget';
    protected ?string $heading = "Used tokens";
    public ?array $usageFilters = ['usageRange' => 'all'];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::rangeToggle($this, 'loadTokens', 'usageRange')
            ])
            ->statePath('usageFilters');
    }

    public function loadWidgetData(): void
    {
        $service = app(UsageService::class);
        $result = $service->getTokens($this->usageFilters['usageRange']);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->widgetData = $result;
        }
    }

    public function openDetails(): void
    {
        $this->redirect(UsageDetail::getUrl());
    }
}
