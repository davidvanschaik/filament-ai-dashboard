<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Unit;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ProjectTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\ProjectUsageTable;
use DavidvanSchaik\FilamentAiDashboard\Models\Message;
use DavidvanSchaik\FilamentAiDashboard\Models\Project;
use DavidvanSchaik\FilamentAiDashboard\Models\Task;
use DavidvanSchaik\FilamentAiDashboard\Models\TaskRun;
use DavidvanSchaik\FilamentAiDashboard\Services\UsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProjectUsageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.frankfurter.app/*' => Http::response([
                'rates' => ['EUR' => 0.92]
            ], 200)
        ]);
    }

    #[Test]
    public function test_if_dataset_for_project_table_returns_correctly(): void
    {
        $table = $this->getProjectTableClass();
        $this->createProjectTestData();

        $table->month = '2025-01';
        $records = $table->records();
        $expected = [
            'Test Project' => [
                'project'      => 'Test Project',
                'total_tokens' => 358500,
                'total_euro'   => 0.15,
            ],
        ];

        $this->assertEquals($expected, $records);
    }


    #[Test]
    public function test_if_dataset_for_project_token_chart_returns_correctly(): void
    {
        $chart = $this->getProjectTokenChartClass();
        $this->createProjectTestData();

        $projectDailyUsage = app(UsageService::class)->sortProjectDailyUsage('2025-01');
        $dataset = $chart->dataset($projectDailyUsage, 31);
        $expected = [
            [
                'label' => 'Test Project',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 72000, 51000, 56200, 61500, 45800, 72000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#6366f1',
                'fill' => false,
                'lineTension' => 0.3,
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_dataset_for_project_euro_chart_returns_correctly(): void
    {
        $chart = $this->getProjectTokenChartClass();
        $this->createProjectTestData();

        $projectDailyUsage = app(UsageService::class)->sortProjectDailyUsage('2025-01');

        $chart->data = ['convert' => true];
        $dataset = $chart->dataset($projectDailyUsage, 31);
        $expected = [
            [
                'label' => 'Test Project',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0.04, 0.03, 0.03, 0.03, 0.02, 0.04, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#6366f1',
                'fill' => false,
                'lineTension' => 0.3,
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    protected function createProjectTestData(string $month = '2025-01'): void
    {
        config()->set('filament-ai-dashboard-pricing', [
            'default' => [
                'input_tokens'  => 0,
                'cached_tokens' => 0,
                'output_tokens' => 0,
            ],
            'gpt-4o' => [
                'input_tokens'  => 0.5,
                'cached_tokens' => 0.25,
                'output_tokens' => 1.5,
            ]
        ]);

        $project = Project::create(['name' => 'Test Project']);

        $tasks = [
            Task::create(['name' => 'Data Processing']),
            Task::create(['name' => 'Report Generation']),
            Task::create(['name' => 'Email Notifications']),
        ];

        $startDate = Carbon::parse($month)->startOfMonth();

        $tokenPatterns = [
            ['input' => 50000, 'cached' => 10000, 'output' => 1000, 'duration' => 120000],
            ['input' => 55000, 'cached' => 12000, 'output' => 1200, 'duration' => 120000],
            ['input' => 60000, 'cached' => 15000, 'output' => 1500, 'duration' => 120000],
            ['input' => 45000, 'cached' => 8000, 'output' => 800, 'duration' => 120000],
            ['input' => 70000, 'cached' => 18000, 'output' => 2000, 'duration' => 120000],
        ];

        foreach ($tasks as $taskIndex => $task) {
            foreach ($tokenPatterns as $dayOffset => $tokens) {
                $createdAt = $startDate->copy()->addDays($dayOffset + ($taskIndex * 5));

                $message = Message::create([
                    'project_id' => $project->id,
                    'type' => 'task',
                    'input_tokens' => $tokens['input'],
                    'input_cached_tokens' => $tokens['cached'],
                    'output_tokens' => $tokens['output'],
                    'metadata' => json_encode(['model' => 'gpt-4o-2024-08-06']),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                TaskRun::create([
                    'task_id' => $task->id,
                    'message_id' => $message->id,
                    'duration' => $tokens['duration'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    private function getProjectTokenChartClass(): ProjectTokenEuroChart
    {
        return new class extends ProjectTokenEuroChart
        {
            public function dataset(array $projectDailyUsage, int $daysInMonth): array
            {
                return $this->createDatasetForChart($projectDailyUsage, $daysInMonth);
            }
        };
    }

    private function getProjectTableClass(): ProjectUsageTable
    {
        return new class extends ProjectUsageTable
        {
            public function records(): array
            {
                return $this->getRecords();
            }
        };
    }
}
