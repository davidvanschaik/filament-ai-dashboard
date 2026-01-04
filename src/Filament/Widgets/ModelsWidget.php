<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\ModelsDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class ModelsWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.widgets.models-widget';

    protected ?string $heading = "Top 3 Models";

    public array $models = [];

    public ?string $errorMessage = null;

    public ?array $modelFilters = ['modelRange' => 'all'];

    public function mount(): void
    {
        $this->loadModels();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::rangeToggle($this, 'loadModels', 'modelRange')
            ])
        ->statePath('modelFilters');
    }

    public function loadModels(): void
    {
        $service = app(AiModelService::class);
        $result = $service->getMostUsedModels(3, $this->modelFilters['modelRange']);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->models = $result;
        }
    }

    public function openDetails(): void
    {
        $this->redirect(ModelsDetail::getUrl());
    }
}
