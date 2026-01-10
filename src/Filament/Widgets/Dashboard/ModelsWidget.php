<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Dashboard;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\Detail\ModelsDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class ModelsWidget extends BaseDashboardWidget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.widgets.dashboard.models-widget';
    protected ?string $heading = "Top 3 Models";
    public ?array $modelFilters = ['modelRange' => 'all'];

    public function loadWidgetData(): void
    {
        $service = app(AiModelService::class);
        $result = $service->getMostUsedModels(3, $this->modelFilters['modelRange']);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->widgetData = $result;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::rangeToggle($this, 'loadModels', 'modelRange')
            ])
            ->statePath('modelFilters');
    }

    public function openDetails(): void
    {
        $this->redirect(ModelsDetail::getUrl());
    }
}
