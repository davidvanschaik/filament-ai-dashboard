<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Unit;

use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAIClient;
use DavidvanSchaik\FilamentAiDashboard\Services\TimeRangeService;
use DavidvanSchaik\FilamentAiDashboard\Services\UsageService;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OpenAiTokenEuroUsageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
    }

    #[Test]
    public function test_it_calculates_total_tokens_and_correct_euro_pricing(): void
    {
        Http::fake([
            'api.frankfurter.app/*' => Http::response([
                'rates' => ['EUR' => 1.0]
            ], 200)
        ]);

        config()->set('filament-ai-dashboard-pricing', [
            'default' => [
                'input_tokens'  => 0,
                'cached_tokens' => 0,
                'output_tokens' => 0,
            ],
            'gpt-4-turbo'   => [
                'input_tokens'  => 0.5,
                'cached_tokens' => 0.25,
                'output_tokens' => 1.5,
            ],
            'gpt-4o'        => [
                'input_tokens'  => 1,
                'cached_tokens' => 0.5,
                'output_tokens' => 1.25,
            ],
            'o3-mini'       => [
                'input_tokens'  => 1.25,
                'cached_tokens' => 0.75,
                'output_tokens' => 1.50,
            ],
            'gpt-3.5-turbo' => [
                'input_tokens'  => 0.25,
                'cached_tokens' => 0.1,
                'output_tokens' => 0.5,
            ],
            'gpt-4o-mini'   => [
                'input_tokens'  => 0.5,
                'cached_tokens' => 0.25,
                'output_tokens' => 0.75,
            ],
        ]);

        $this->bindUsagePayload();

        $result = app(UsageService::class)->getTokens('month');
        $expected = [
            'tokens' => 1190150,
            'euro' => 1.3622375
        ];

        $this->assertSame($expected, $result);
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
        app()->instance(TimeRangeService::class, $time);

        $result = app(UsageService::class)->getTokens('month');
        $this->assertSame(['Error' => 'No data found.'], $result);
    }

    private function bindUsagePayload(): void
    {
        $payload = [
            'data' => [
                [
                    'results' => [
                        [
                            'model' => 'gpt-4-turbo',
                            'num_model_requests' => 8,
                            'input_tokens' => 100000,
                            'input_cached_tokens' => 500,
                            'output_tokens' => 10000,
                        ],
                        [
                            'model' => 'gpt-4o-2024-08-06',
                            'num_model_requests' => 4,
                            'input_tokens' => 150000,
                            'input_cached_tokens' => 450,
                            'output_tokens' => 200000,
                        ],
                    ]
                ],
                [
                    'results' => [
                        [
                            'model' => 'o3-mini',
                            'num_model_requests' => 6,
                            'input_tokens' => 300000,
                            'input_cached_tokens' => 40000,
                            'output_tokens' => 340000,
                        ],
                        [
                            'model' => 'gpt-3.5-turbo',
                            'num_model_requests' => 5,
                            'input_tokens' => 50000,
                            'input_cached_tokens' => 0,
                            'output_tokens' => 40000,
                        ],
                        [
                            'model' => 'gpt-4o-mini',
                            'num_model_requests' => 3,
                            'input_tokens' => 100,
                            'input_cached_tokens' => 0,
                            'output_tokens' => 50,
                        ],
                    ]
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

        app()->instance(TimeRangeService::class, $time);
    }
}
