<?php

namespace DavidvanSchaik\FilamentAiDashboard\Database\Factories;

use DavidvanSchaik\FilamentAiDashboard\Models\TaskRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskRun>
 */
class TaskRunFactory extends Factory
{
    protected $model = TaskRun::class;

    public function definition(): array
    {
        return [
            'task_id' => null,
            'message_id' => null,
            'duration' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
