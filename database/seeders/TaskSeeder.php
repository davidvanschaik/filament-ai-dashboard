<?php

namespace DavidvanSchaik\FilamentAiDashboard\Database\Seeders;

use DavidvanSchaik\FilamentAiDashboard\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            'Samenvatten',
            'Risico-analyse',
            'Concept calculatie',
            'Offerte genereren',
            'Plan van aanpak',
            'Kostenindicatie'
        ];

        foreach ($tasks as $task) {
            Task::create([
                'name' => $task,
            ]);
        }
    }
}
