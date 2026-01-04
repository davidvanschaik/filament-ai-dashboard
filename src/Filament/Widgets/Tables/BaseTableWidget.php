<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables;

use Filament\Widgets\TableWidget;
use Livewire\Attributes\On;

abstract class BaseTableWidget extends TableWidget
{
    public ?string $month = null;

    protected $listeners = [
        'monthChanged' => 'onMonthChanged',
    ];

    #[On('MonthChanged')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
    }

    abstract protected function getRecords(): array;
}
