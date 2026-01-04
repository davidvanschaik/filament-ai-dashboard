<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Unit;

use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAIClient;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\BaseChartWidget;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsRequestChart;
use DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\Charts\ModelsTokenEuroChart;
use DavidvanSchaik\FilamentAiDashboard\Services\AiModelService;
use DavidvanSchaik\FilamentAiDashboard\Services\TimeRangeService;
use DavidvanSchaik\FilamentAiDashboard\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OpenAiModelsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_if_dataset_with_total_request_sorts_correctly(): void
    {
        $this->bindPayload();
        $result = (new AiModelService(new TokenService()))->getMostUsedModels(3, 'month');

        $this->assertSame([
            'GPT 4' => 555,
            'GPT 3' => 2
        ], $result);
    }

    #[Test]
    public function test_if_no_data_is_available_and_returns_correctly(): void
    {
        $client = Mockery::mock(OpenAIClient::class);
        $client->shouldReceive('getUsage')
            ->andReturn(['data' => []])
            ->byDefault();
        app()->instance(OpenAIClient::class, $client);

        $time = Mockery::mock(TimeRangeService::class);
        $time->shouldReceive('getThisMonthTimestamps')->andReturn([1000, 2000])->byDefault();
        $time->shouldReceive('getMonthTimeStamps')->andReturn([1000, 2000])->byDefault();
        $time->shouldReceive('parseToTimestamp')->andReturn([1000, 2000])->byDefault();
        app()->instance(TimeRangeService::class, $time);

        $result = app(AiModelService::class)->getMostUsedModels(3, 'month');
        $this->assertSame(['Error' => 'No data found.'], $result);
    }

    #[Test]
    public function test_if_dataset_with_requests_per_day_returns_correctly(): void
    {
        $this->bindPayload();

        $chartBaseClass = $this->getChartWidgetClass();
        $modelsChart = $this->getRequestChartClass();

        $month = '2025-03';

        $results = app(AiModelService::class)->getModelRequests($month);
        $labels = $chartBaseClass->days($month);
        $datasets = $modelsChart->dataset($results, end($labels));

        $expected = [
            [
                'label' => 'gpt-4.5-preview-2025-02-27',
                'data' => [0, 0, 0, 0, 0, 0, 87, 0, 0, 245, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#6366f1',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-4-0125-preview',
                'data' => [0, 0, 0, 0, 0, 0, 19, 0, 0, 14, 46, 26, 83, 32, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#16a34a',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-4o-mini-2024-07-18',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#9333ea',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-3.5-turbo-0125',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#2563eb',
                'fill' => false,
                'lineTension' => 0.3,
            ],
        ];

        $this->assertSame($expected, $datasets);
    }

    #[Test]
    public function test_if_datasets_with_tokens_per_day_returns_correctly(): void
    {
        $this->bindPayload();

        $chartBaseClass = $this->getChartWidgetClass();
        $tokenChart = $this->getTokensChartClass();

        $month = '2025-03';

        $tokens = app(AiModelService::class)->getModelTokens($month);
        $labels = $chartBaseClass->days($month);
        $datasets = $tokenChart->dataset($tokens, end($labels));

        $expected = [
            [
                'label' => 'gpt-4.5-preview-2025-02-27',
                'data' => [0, 0, 0, 0, 0, 0, 590291, 0, 0, 1032038, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#6366f1',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-4-0125-preview',
                'data' => [0, 0, 0, 0, 0, 0, 26189, 0, 0, 12306, 55551, 88458, 176941, 46849, 700, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#16a34a',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-4o-mini-2024-07-18',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1678, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#9333ea',
                'fill' => false,
                'lineTension' => 0.3,
            ],
            [
                'label' => 'gpt-3.5-turbo-0125',
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 301, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => '#2563eb',
                'fill' => false,
                'lineTension' => 0.3,
            ],
        ];

        $this->assertSame($expected, $datasets);
    }

    #[Test]
    public function test_if_model_statistics_return_correctly(): void
    {
        $this->bindPayload();
        $month = '2025-03';

        $statistics = app(AiModelService::class)->getModelStatistics($month);
        $expected = [
            'gpt-4.5-preview-2025-02-27' => [
                'model' => 'gpt-4.5-preview-2025-02-27',
                'requests' => 332,
                'total_tokens' => 1622329,
                'total_euro' => 3.82,
                'average_token_per_request' => 4887.0,
                'average_euro_per_request' => 0.01,
            ],
            'gpt-4-0125-preview' => [
                'model' => 'gpt-4-0125-preview',
                'requests' => 221,
                'total_tokens' => 406994,
                'total_euro' => 0.24000000000000002,
                'average_token_per_request' => 1842.0,
                'average_euro_per_request' => 0.0,
            ],
            'gpt-4o-mini-2024-07-18' => [
                'model' => 'gpt-4o-mini-2024-07-18',
                'requests' => 2,
                'total_tokens' => 1678,
                'total_euro' => 0.0,
                'average_token_per_request' => 839.0,
                'average_euro_per_request' => 0.0,
            ],
            'gpt-3.5-turbo-0125' => [
                'model' => 'gpt-3.5-turbo-0125',
                'requests' => 2,
                'total_tokens' => 301,
                'total_euro' => 0.0,
                'average_token_per_request' => 151.0,
                'average_euro_per_request' => 0.0,
            ],
        ];

        $this->assertSame($expected, $statistics);
    }

    private function bindPayload(): void
    {
        config()->set('filament-ai-dashboard-pricing', [
            'default' => [
                'input_tokens' => 0,
                'cached_tokens' => 0,
                'output_tokens' => 0,
            ],
            'gpt-4-0125-preview' => [
                'input_tokens' => 0.5,
                'cached_tokens' => 0.25,
                'output_tokens' => 1.5,
            ],
            'gpt-4.5' => [
                'input_tokens' => 1,
                'cached_tokens' => 0.5,
                'output_tokens' => 1.25,
            ],
            'gpt-4o-mini-2024-07-18' => [
                'input_tokens' => 1.25,
                'cached_tokens' => 0.75,
                'output_tokens' => 1.50,
            ],
            'gpt-3.5-turbo' => [
                'input_tokens' => 0.25,
                'cached_tokens' => 0.1,
                'output_tokens' => 0.5,
            ]
        ]);

        $payload = [
            'data' => [
                [
                    'object' => 'bucket',
                    'start_time' => 1741305600,
                    'end_time' => 1741392000,
                    'start_time_iso' => '2025-03-07T00:00:00+00:00',
                    'end_time_iso' => '2025-03-08T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 87,
                            'model' => 'gpt-4.5-preview-2025-02-27',
                            'input_tokens' => 1214189,
                            'output_tokens' => 22758,
                            'input_cached_tokens' => 646656,
                        ],
                        [
                            'num_model_requests' => 19,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 21990,
                            'output_tokens' => 4199,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741564800,
                    'end_time' => 1741651200,
                    'start_time_iso' => '2025-03-10T00:00:00+00:00',
                    'end_time_iso' => '2025-03-11T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 245,
                            'model' => 'gpt-4.5-preview-2025-02-27',
                            'input_tokens' => 4701502,
                            'output_tokens' => 45352,
                            'input_cached_tokens' => 3714816,
                        ],
                        [
                            'num_model_requests' => 14,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 11508,
                            'output_tokens' => 798,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741651200,
                    'end_time' => 1741737600,
                    'start_time_iso' => '2025-03-11T00:00:00+00:00',
                    'end_time_iso' => '2025-03-12T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 2,
                            'model' => 'gpt-4o-mini-2024-07-18',
                            'input_tokens' => 3117,
                            'output_tokens' => 97,
                            'input_cached_tokens' => 1536,
                        ],
                        [
                            'num_model_requests' => 46,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 50782,
                            'output_tokens' => 4769,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741737600,
                    'end_time' => 1741824000,
                    'start_time_iso' => '2025-03-12T00:00:00+00:00',
                    'end_time_iso' => '2025-03-13T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 26,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 85227,
                            'output_tokens' => 3231,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741824000,
                    'end_time' => 1741910400,
                    'start_time_iso' => '2025-03-13T00:00:00+00:00',
                    'end_time_iso' => '2025-03-14T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 83,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 161568,
                            'output_tokens' => 15373,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741910400,
                    'end_time' => 1741996800,
                    'start_time_iso' => '2025-03-14T00:00:00+00:00',
                    'end_time_iso' => '2025-03-15T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 32,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 41556,
                            'output_tokens' => 5293,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
                [
                    'object' => 'bucket',
                    'start_time' => 1741996800,
                    'end_time' => 1742083200,
                    'start_time_iso' => '2025-03-15T00:00:00+00:00',
                    'end_time_iso' => '2025-03-16T00:00:00+00:00',
                    'results' => [
                        [
                            'num_model_requests' => 2,
                            'model' => 'gpt-3.5-turbo-0125',
                            'input_tokens' => 39,
                            'output_tokens' => 262,
                            'input_cached_tokens' => 0,
                        ],
                        [
                            'num_model_requests' => 1,
                            'model' => 'gpt-4-0125-preview',
                            'input_tokens' => 38,
                            'output_tokens' => 662,
                            'input_cached_tokens' => 0,
                        ],
                    ],
                ],
            ]
        ];

        $client = Mockery::mock(OpenAIClient::class);
        $client->shouldReceive('getUsage')
            ->andReturn($payload)
            ->byDefault();
        app()->instance(OpenAIClient::class, $client);

        $time = Mockery::mock(TimeRangeService::class);
        $time->shouldReceive('getThisMonthTimestamps')
            ->andReturn([1000, 2000])
            ->byDefault();

        $time->shouldReceive('getMonthTimeStamps')
            ->andReturn([1000, 2000])
            ->byDefault();

        $time->shouldReceive('parseToTimestamp')
            ->andReturn([1000, 2000])
            ->byDefault();

        $time->shouldReceive('parseToDate')
            ->andReturnUsing(fn($timestamp) => date('d', $timestamp))
            ->byDefault();
        $time->shouldReceive('daysInMonth')
            ->andReturn(31)
            ->byDefault();

        $time->shouldReceive('daysInMonth')
            ->andReturn(31)
            ->byDefault();

        app()->instance(TimeRangeService::class, $time);

        Http::fake([
            'api.frankfurter.app/*' => Http::response([
                'rates' => ['EUR' => 1.0]
            ], 200)
        ]);
    }

    private function getChartWidgetClass(): BaseChartWidget
    {
        return new class extends BaseChartWidget {
            public function days(string $month): array
            {
                return $this->getDaysInMonth($month);
            }
        };
    }

    private function getRequestChartClass(): ModelsRequestChart
    {
        return new class extends ModelsRequestChart {
            public function dataset(array $requests, int $days): array
            {
                return $this->createDatasetForChart($requests, $days);
            }
        };
    }

    private function getTokensChartClass(): ModelsTokenEuroChart
    {
        return new class extends ModelsTokenEuroChart {
            public function dataset(array $requests, int $days): array
            {
                return $this->createDatasetForChart($requests, $days);
            }
        };
    }
}
