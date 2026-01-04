<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets;

use DavidvanSchaik\FilamentAiDashboard\Filament\Components\FilterComponents;
use DavidvanSchaik\FilamentAiDashboard\Filament\Pages\UsageDetail;
use DavidvanSchaik\FilamentAiDashboard\Services\UsageService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class UsageWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-ai-dashboard::filament.widgets.usage-widget';

    protected ?string $heading = "Used tokens";

    public array $usage = [];

    public ?string $errorMessage = null;

    public ?array $usageFilters = ['usageRange' => 'all'];

    public function mount(): void
    {
        $this->loadTokens();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FilterComponents::rangeToggle($this, 'loadTokens', 'usageRange')
            ])
            ->statePath('usageFilters');
    }

    public function loadTokens(): void
    {
        $service = app(UsageService::class);
        $result = $service->getTokens($this->usageFilters['usageRange']);

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->usage = $result;
        }
    }

    public function openDetails(): void
    {
        $this->redirect(UsageDetail::getUrl());
    }
}
