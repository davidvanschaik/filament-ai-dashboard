<?php

namespace DavidvanSchaik\FilamentAiDashboard\Database\Seeders;

use DavidvanSchaik\FilamentAiDashboard\Models\Message;
use DavidvanSchaik\FilamentAiDashboard\Models\Project;
use DavidvanSchaik\FilamentAiDashboard\Models\Task;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::query()->pluck('id')->toArray();
        $tasks = Task::query()->pluck('id')->toArray();

        if (empty($projects)) {
            $this->command->warn('Geen projecten gevonden. Maak of seed eerst projecten');
            return;
        }

        $months = collect(range(0, 8))
            ->map(fn ($month) => now()->startOfMonth()->subMonths($month))
            ->reverse();

        foreach ($months as $month) {
            Message::factory()
                ->count(random_int(60, 100))
                ->forProject($projects)
                ->messageWithCachedTokens()
                ->modelsWithCachedTokens()
                ->inMonth($month)
                ->create();

            Message::factory()
                ->count(random_int(60, 100))
                ->forProject($projects)
                ->messageWithoutCachedTokens()
                ->modelsWithoutCachedTokens()
                ->inMonth($month)
                ->create();

            Message::factory()
                ->count(random_int(20, 50))
                ->forProject($projects)
                ->taskWithoutCachedTokens($tasks, $month)
                ->modelsWithoutCachedTokens()
                ->inMonth($month)
                ->create();

            Message::factory()
                ->count(random_int(20, 50))
                ->forProject($projects)
                ->taskWithCachedTokens($tasks, $month)
                ->modelsWithoutCachedTokens()
                ->inMonth($month)
                ->create();
        }

        $this->command->info('Message records voor 6 maanden gemaakt!');
    }
}
