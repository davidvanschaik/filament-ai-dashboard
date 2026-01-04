<?php

namespace DavidvanSchaik\FilamentAiDashboard\Database\Seeders;

use DavidvanSchaik\FilamentAiDashboard\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AiDashboardDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            TaskSeeder::class,
            ProjectSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
