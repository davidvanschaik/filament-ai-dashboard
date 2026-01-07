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

        $this->ensureFilamentThemeAndSource();

        if ($this->confirm('Run migrations now? [Y/n]', true)) {
            $this->call('migrate');
        }

        if ($this->confirm('Run seeders now? [Y/n]', true)) {
            $this->call('db:seed', [
                '--class' => 'DavidvanSchaik\\FilamentAiDashboard\\Database\\Seeders\\AiDashboardDatabaseSeeder',
            ]);
        }

        $this->newLine();
        $this->info('Installed Filament AI Dashboard');
        $this->newLine();
        $this->warn('Next steps:');
        $this->newLine();
        $this->info('- Add the .env variables with the command php artisan filament-ai-dashboard:install-env');
        $this->newLine();
        $this->info('- Add to app/Providers/Filament/...ServiceProvider ->plugins(AiDashboardPlugin::make()');
        $this->newLine();
        $this->info('- Add @source line to your filament theme.css by running the command filament-ai-dashboard:make-theme');

        return self::SUCCESS;
    }
}
