<?php

namespace Database\Seeders;

use DavidvanSchaik\FilamentAiDashboard\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 5; $i++) {
            Project::create([
                'name' => "Project $i",
            ]);
        }
    }
}
