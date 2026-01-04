<?php

namespace DavidvanSchaik\FilamentAiDashboard\Console;

use Illuminate\Console\Command;

class InstallFilamentAiDashboardCommand extends Command
{
    protected $signature = 'filament-ai-dashboard:install';
    protected $description = 'Install all of the Filament AI dashboard';

    public function handle(): int
    {
        $this->info('Publishing configure files...');
        $this->call('vendor:publish', [
            '--tag' => 'filament-ai-dashboard-config',
            '--force' => (bool) $this->option('force'),
        ]);

        if ($this->confirm('Run migrations now? [Y/n]', true)) {
            $this->call('migrate');
        }

        if ($this->confirm('Run seeders now? [Y/n]', true)) {
            $this->call('db:seed', [
                '--class' => 'DavidvanSchaik\\FilamentAiDashboard\\Database\\Seeders\\DatabaseSeeder',
            ]);
        }

        $this->info('Installed Filament AI dashboard');
        return self::SUCCESS;
    }
}
