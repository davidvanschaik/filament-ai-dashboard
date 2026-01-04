<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Components;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;

class FilterComponents
{
    public static function rangeToggle(object $class, string $method, string $name): ToggleButtons
    {
        return ToggleButtons::make($name)
            ->inline()
            ->grouped()
            ->live()
            ->afterStateUpdated(fn() => $class->{$method}())
            ->hiddenLabel()
            ->options([
                'all' => 'All time',
                'month' => 'This month'
            ]);
    }

    public static function monthPicker(): TextInput
    {
        return TextInput::make('month')
            ->inlineLabel()
            ->label('Kies een maand')
            ->type('month')
            ->default(now()->format('Y-m'))
            ->live();
    }

    public static function convertToggle(): Toggle
    {
        return Toggle::make('convert')
            ->hiddenLabel()
            ->onIcon('heroicon-o-currency-euro')
            ->offIcon('heroicon-o-circle-stack')
            ->live();
    }
}
