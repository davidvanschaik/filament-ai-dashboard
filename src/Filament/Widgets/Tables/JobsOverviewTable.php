<?php

namespace DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables;

use DavidvanSchaik\FilamentAiDashboard\Services\JobService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobsOverviewTable extends BaseTableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                TextColumn::make('job')
                    ->label('Job')
                    ->weight('bold'),
                TextColumn::make('execution_count')
                    ->label('Execution Count')
                    ->numeric(),
                TextColumn::make('total_tokens')
                    ->label('Total Tokens')
                    ->numeric(),
                TextColumn::make('total_euro')
                    ->label('Total Euro'),
                TextColumn::make('average_duration')
                    ->label('Avg Duration')
                    ->numeric(),
            ]);
    }

    protected function getRecords(): array
    {
        return app(JobService::class)->getJobsStatistics($this->month);
    }
}
