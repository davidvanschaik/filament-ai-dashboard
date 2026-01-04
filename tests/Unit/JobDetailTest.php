<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Unit;

use Carbon\Carbon;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsDurationChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsExecutedChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\JobsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Tables\JobsOverviewTable;
use DavidvanSchaik\FilamentAiDashboard\Models\Message;
use DavidvanSchaik\FilamentAiDashboard\Models\Project;
use DavidvanSchaik\FilamentAiDashboard\Models\Task;
use DavidvanSchaik\FilamentAiDashboard\Models\TaskRun;
use DavidvanSchaik\FilamentAiDashboard\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JobDetailTest extends TestCase
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
    public function test_if_dataset_with_executed_jobs_returns_correctly(): void
    {
        $chart = $this->getJobExecutedChartClass();
        $jobs = $this->createJobsTestData();

        $dataset = $chart->dataset($jobs);
        $expected = [
            'labels' => [
                'Data Processing',
                'Report Generation',
                'Email Notifications',
            ],
            'datasets' => [
                [
                    'label' => 'Executions',
                    'data' => [5, 5, 5],
                    'backgroundColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                    'borderColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_dataset_with_job_duration_returns_correctly(): void
    {
        $chart = $this->getJobDurationChartClass();
        $jobs = $this->createJobsTestData();

        $dataset = $chart->dataset($jobs);
        $expected = [
            'labels' => [
                'Data Processing',
                'Report Generation',
                'Email Notifications',
            ],
            'datasets' => [
                [
                    'label' => 'Duration in minutes',
                    'data' => [
                        'Data Processing'     => 2000.0,
                        'Report Generation'   => 2000.0,
                        'Email Notifications' => 2000.0,
                    ],
                    'backgroundColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                    'borderColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_dataset_with_job_tokens_returns_correctly(): void
    {
        $dataset = $this->jobTokenEuroDataset(false);
        $expected = [
            'labels' => [
                'Data Processing',
                'Report Generation',
                'Email Notifications',
            ],
            'datasets' => [
                [
                    'label' => 'Jobs Total Tokens',
                    'data' => [
                        'Data Processing'   => 286500,
                        'Report Generation' => 286500,
                        'Email Notifications' => 286500,
                    ],
                    'backgroundColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                    'borderColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_dataset_with_job_euro_returns_correctly(): void
    {
        $dataset = $this->jobTokenEuroDataset(true);
        $expected = [
            'labels' => [
                'Data Processing',
                'Report Generation',
                'Email Notifications',
            ],
            'datasets' => [
                [
                    'label' => 'Jobs Total Euro',
                    'data' => [
                        'Data Processing'     => 0.15,
                        'Report Generation'   => 0.15,
                        'Email Notifications' => 0.15,
                    ],
                    'backgroundColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                    'borderColor' => [
                        '#6366f1',
                        '#16a34a',
                        '#9333ea',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_dataset_with_job_data_for_table_returns_correctly(): void
    {
        $table = $this->getJobTableClass();
        $this->createJobsTestData();

        $table->month = '2025-01';

        $dataset = $table->records();
        $expected = [
            'Data Processing' => [
                'job'              => 'Data Processing',
                'execution_count'  => 5,
                'total_tokens'     => 286500,
                'total_euro'       => '€ 0.15',
                'average_duration' => 2000.0,
            ],
            'Report Generation' => [
                'job'              => 'Report Generation',
                'execution_count'  => 5,
                'total_tokens'     => 286500,
                'total_euro'       => '€ 0.15',
                'average_duration' => 2000.0,
            ],
            'Email Notifications' => [
                'job'              => 'Email Notifications',
                'execution_count'  => 5,
                'total_tokens'     => 286500,
                'total_euro'       => '€ 0.15',
                'average_duration' => 2000.0,
            ],
        ];

        $this->assertEquals($expected, $dataset);
    }

    #[Test]
    public function test_if_no_data_is_available_returns_correctly(): void
    {
        $result = app(JobService::class)->getExecutedJobs(3);
        $this->assertSame(['Error' => 'No jobs found.'], $result);
    }

    private function jobTokenEuroDataset(bool $convert): array
    {
        $chart = $this->getJobTokenChartClass();
        $this->createJobsTestData();

        $jobs = app(JobService::class)->getJobUsedTokens('2025-01');

        $chart->data = ['convert' => $convert];

        return $chart->dataset($jobs);
    }

    protected function createJobsTestData(string $month = '2025-01'): array
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
            ],
        ]);

        $project = Project::create(['name' => 'Test Project',]);

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

        return app(JobService::class)->getExecutedJobsFromTimeRange($month);
    }

    private function getJobExecutedChartClass(): JobsExecutedChart
    {
        return new class extends JobsExecutedChart
        {
            public function dataset(array $jobs): array
            {
                return $this->createDatasetForChart($jobs);
            }
        };
    }

    private function getJobTokenChartClass(): JobsTokenEuroChart
    {
        return new class extends JobsTokenEuroChart
        {
            public function dataset(array $jobs): array
            {
                return $this->createDatasetForChart($jobs);
            }
        };
    }

    private function getJobDurationChartClass(): JobsDurationChart
    {
        return new class extends JobsDurationChart
        {
            public function dataset(array $jobs): array
            {
                return $this->createDatasetForChart($jobs);
            }
        };
    }

    private function getJobTableClass(): JobsOverviewTable
    {
        return new class extends JobsOverviewTable
        {
            public function records(): array
            {
                return $this->getRecords();
            }
        };
    }
}
