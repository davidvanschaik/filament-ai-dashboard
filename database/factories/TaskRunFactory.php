<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => null,
            'message_id' => null,
            'duration' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
