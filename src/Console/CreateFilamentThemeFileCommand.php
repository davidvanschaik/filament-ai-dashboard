<?php

namespace DavidvanSchaik\FilamentAiDashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateFilamentThemeFileCommand extends Command
{
    protected $signature = 'filament-ai-dashboard:theme';
    protected $description = 'Adding source line to theme file or create a new filament theme file';

    public function handle(): void
    {
        $this->info('Adding source line to theme file...');

        $this->ensureFilamentThemeAndSource();
    }

    private function ensureFilamentThemeAndSource(): void
    {
        $themeFiles = $this->findThemeFiles();

        if (empty($themeFiles)) {
            $this->warn('No Filament theme.css file found');
            $this->newLine();
            $this->info('Creating theme.css file...');

            $this->call('make:filament-theme');

            $themeFiles = $this->findThemeFiles();
        }

        if (empty($themeFiles)) {
            $this->error('Filament theme could not be found or created. Skipping @source injection.');
            return;
        }

        $added = 0;
        foreach ($themeFiles as $path) {
            if ($this->ensureSourceLineInThemeCss($path)) {
                $added++;
            }
        }

        if ($added > 0) {
            $this->info("Added @source path to {$added} Filament theme.css file(s).");
            $this->line('You should rebuild your assets (npm run build / npm run dev).');
        } else {
            $this->info('Filament theme.css already contained the required @source path.');
        }
    }

    private function findThemeFiles(): array
    {
        $themeFile = 'resources/css/filament/**/theme.css';
        $source =  '@source "../../../../vendor/davidvanschaik/filament-ai-dashboard/resources/views/**/*.blade.php";';

        $paths = glob(base_path($themeFile));
        $single = base_path('resources/css/filament/theme.css');

        if (File::exists($single)) {
            $paths[] = $single;
        }

        return $paths;
    }

    private function ensureSourceLineInThemeCss(string $path): bool
    {
        $source = '@source "../../../../vendor/davidvanschaik/filament-ai-dashboard/resources/views/**/*.blade.php";';
        $contents = File::get($path);

        if (str_contains($contents, $source)) {
            return false;
        }

        $contents = rtrim($contents) . PHP_EOL . $source . PHP_EOL;

        File::put($path, $contents);

        return true;
    }
}
