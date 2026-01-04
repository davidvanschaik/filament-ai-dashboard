<?php

namespace DavidvanSchaik\FilamentAiDashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishEnvVariablesCommand extends Command
{
    protected $signature = 'filament-ai-dashboard:publish-env';
    protected $description = 'Add required environment variables to .env';

    public function handle(): int
    {
        $choice = $this->choice('Add local endpoints or OpenAI endpoints?', ['local', 'openai']);
        $baseEndpoint = 'https://api.openai.com/';

        if ($choice == 'local') {
            $baseEndpoint = 'http://127.0.0.1:3000/api/ai-dashboard/';
        }

        $this->info('Adding variables...');
        $this->addEnvVariableIfNotExists('FILAMENT_AI_DASHBOARD_OPENAI_ADMIN_API_KEY', '');
        $this->addEnvVariableIfNotExists('FILAMENT_AI_DASHBOARD_OPENAI_API_KEY', '');

        $endpoints = [
            'FILAMENT_AI_DASHBOARD_API_MODELS_ENDPOINT' => 'v1/organization/usage/completions',
            'FILAMENT_AI_DASHBOARD_API_VECTOR_STORE_ENDPOINT' => 'v1/vector_stores',
            'FILAMENT_AI_DASHBOARD_API_FILES_ENDPOINT' => 'v1/files'
        ];

        foreach ($endpoints as $key => $value) {
            $endpoint = $baseEndpoint . $value;
            $this->addEnvVariableIfNotExists($key, $endpoint);
        }

        $this->info('Environment variables have been added.');
        $this->info('Don\'t forget to set the OpenAI API keys');
        return self::SUCCESS;
    }

    private function addEnvVariableIfNotExists(string $key, string $value): void
    {
        $envFile = base_path('.env');

        if (! File::exists($envFile)) {
            $this->warn('.env file not found.');

            return;
        }

        $env = File::get($envFile);
        $line = PHP_EOL . "{$key}={$value}";

        if (preg_match("/^{$key}=.*/m", $env)) {
            $this->line("{$key} already exists.");

            if ($this->confirm('Overwrite existing key? [Y/n]', true)) {
                File::append($envFile, $line);
            }

            return;
        }
        File::append($envFile, $line);
    }
}
