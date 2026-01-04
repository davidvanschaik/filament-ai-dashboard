<?php

namespace DavidvanSchaik\FilamentAiDashboard\Database\Factories;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Models\Message;
use DavidvanSchaik\FilamentAiDashboard\Models\TaskRun;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => null,
            'type' => 'message',
            'input_tokens' => 0,
            'output_tokens' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function messageWithCachedTokens(): static
    {
        return $this->state(function () {
            return [
                'type' => 'message',
                'input_tokens' => $this->faker->numberBetween(2000, 8000),
                'input_cached_tokens' => $this->faker->numberBetween(500, 2000),
                'output_tokens' => $this->faker->numberBetween(500, 2000),
            ];
        });
    }

    public function messageWithoutCachedTokens(): static
    {
        return $this->state(function () {
            return [
                'type' => 'message',
                'input_tokens' => $this->faker->numberBetween(2000, 8000),
                'input_cached_tokens' => 0,
                'output_tokens' => $this->faker->numberBetween(500, 2000),
            ];
        });
    }

    public function taskWithCachedTokens(array $tasks, Carbon $month): static
    {
        return $this->afterCreating(function ($message) use ($tasks, $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $date = $this->faker->dateTimeBetween($start, $end);

            TaskRun::factory()->create([
                'task_id' => Arr::random($tasks),
                'message_id' => $message->id,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        })->state(function () {
            return [
                'type' => 'task',
                'input_tokens' => $this->faker->numberBetween(40000, 80000),
                'input_cached_tokens' => $this->faker->numberBetween(2000, 20000),
                'output_tokens' => $this->faker->numberBetween(500, 2000),
            ];
        });
    }

    public function taskWithoutCachedTokens(array $tasks, Carbon $month): static
    {
        return $this->afterCreating(function ($message) use ($tasks, $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $date = $this->faker->dateTimeBetween($start, $end);

            TaskRun::factory()->create([
                'task_id' => Arr::random($tasks),
                'message_id' => $message->id,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        })->state(function () {
            return [
                'type' => 'task',
                'input_tokens' => $this->faker->numberBetween(40000, 80000),
                'input_cached_tokens' => 0,
                'output_tokens' => $this->faker->numberBetween(500, 2000),
            ];
        });
    }

    public function inMonth(Carbon $month): static
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return $this->state(function () use ($start, $end) {
            $date = $this->faker->dateTimeBetween($start, $end);

            return [
                'created_at' => $date,
                'updated_at' => $date,
            ];
        });
    }

    public function forProject(array $projects): static
    {
        return $this->state(function () use ($projects) {

            return [
                'project_id' => Arr::random($projects),
            ];
        });
    }

    public function modelsWithCachedTokens(): static
    {
        $models = [
            "gpt-5-2025-08-07",
            "gpt-4.1-2025-04-14",
            "gpt-5.1-2025-11-13",
        ];

        return $this->models($models);
    }

    public function modelsWithoutCachedTokens(): static
    {
        $models = [
            "gpt-4-0125-preview",
            "o3-2025-04-16",
            "gpt-4.5-preview-2025-02-27",
        ];

        return $this->models($models);
    }

    private function models(array $models): static
    {
        return $this->state(function () use ($models) {

            return [
                'metadata' => json_encode(['model' => Arr::random($models)]),
            ];
        });
    }
}
