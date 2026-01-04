<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables;

use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModelOverviewTable extends BaseTableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                TextColumn::make('model')
                    ->label('Model')
                    ->weight('bold'),
                TextColumn::make('requests')
                    ->label('Requests')
                    ->numeric(),
                TextColumn::make('total_tokens')
                    ->label('Total Tokens')
                    ->numeric(),
                TextColumn::make('total_euro')
                    ->label('Total Euro')
                    ->numeric(),
                TextColumn::make('average_token_per_request')
                    ->label('Avg Tokens/Request')
                    ->numeric(),
                TextColumn::make('average_euro_per_request')
                    ->label('Avg Euro/Request')
                    ->numeric(),
            ]);
    }

    protected function getRecords(): array
    {
        return app(AiModelService::class)->getModelStatistics($this->month);
    }
}
