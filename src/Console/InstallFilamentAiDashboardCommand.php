<?php

namespace DavidvanSchaik\FilamentAiDashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallFilamentAiDashboardCommand extends Command
{
    protected $signature = 'filament-ai-dashboard:install {--force : Overwrite existing files}';
    protected $description = 'Install all of the Filament AI dashboard';

    public function handle(): int
    {
        $this->info('Publishing configure files...');
        $this->call('vendor:publish', [
            '--tag' => 'filament-ai-dashboard-config',
            '--force' => (bool)$this->option('force'),
        ]);

        $this->info('Publishing migrations...');
        $this->call('vendor:publish', [
            '--tag' => 'filament-ai-dashboard-migrations',
            '--force' => (bool)$this->option('force'),
        ]);

        $this->info('Publishing data files...');
        $this->call('vendor:publish', [
            '--tag' => 'filament-ai-dashboard-data',
            '--force' => (bool) $this->option('force'),
        ]);

        if ($this->confirm('Run migrations now? [Y/n]', true)) {
            $this->call('migrate');
        }

        if ($this->confirm('Run seeders now? [Y/n]', true)) {
            $this->call('db:seed', [
                '--class' => 'DavidvanSchaik\\FilamentAiDashboard\\Database\\Seeders\\AiDashboardDatabaseSeeder',
            ]);
        }

        $this->info('Installed Filament AI Dashboard');
        return self::SUCCESS;
    }
}
