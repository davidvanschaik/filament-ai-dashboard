<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets;

use DavidvanSchaik\FilamentAiDashboard\Services\StorageService;
use Filament\Widgets\Widget;

class StorageWidget extends Widget
{
    protected string $view = 'filament-ai-dashboard::filament.widgets.storage-widget';

    protected ?string $heading = "Stored data";

    public array $storage = [];

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->loadStorage();
    }

    public function loadStorage(): void
    {
        $service = app(StorageService::class);
        $result = $service->getStorageData();

        if (isset($result['Error'])) {
            $this->errorMessage = $result['Error'];
        } else {
            $this->storage = $result;
        }
    }
}
