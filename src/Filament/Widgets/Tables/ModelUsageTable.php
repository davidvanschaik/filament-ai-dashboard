<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables;

use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModelUsageTable extends BaseTableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                TextColumn::make('model')
                    ->label('Model name')
                    ->weight('bold'),
                TextColumn::make('total_tokens')
                    ->label('Total Tokens')
                    ->numeric(),
                TextColumn::make('total_euro')
                    ->label('Total Euro')
                    ->numeric()
            ]);
    }

    protected function getRecords(): array
    {
        return app(AiModelService::class)->getModelStatistics($this->month);
    }
}
