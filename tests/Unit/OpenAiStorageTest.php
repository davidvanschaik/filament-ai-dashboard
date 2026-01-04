<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Unit;

use DavidvanSchaik\FilamentAiDashboard\Services\BaseStorageService;
use DavidvanSchaik\FilamentAiDashboard\Services\StorageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OpenAiStorageTest extends TestCase
{
    #[Test]
    public function test_it_adds_all_bytes_correctly_and_converts_unit(): void
    {
        $service = $this->getFakeService();

        $payload = [
            'data' => [
                ['bytes' => 500,],
                ['bytes' => 1500,],
                ['bytes' => 2500000,],
            ]
        ];

        [$count, $totalBytes] = $service->testProcess($payload);

        $this->assertSame(3, $count);
        $this->assertSame(2502000, $totalBytes);

        $converted = $service->testConvert($totalBytes);
        $this->assertSame('2.39 MB', $converted);
    }

    #[Test]
    public function test_if_no_data_is_available_and_returns_correctly(): void
    {
        Cache::shouldReceive('get')->with('vector_store')->andReturn(null);
        Cache::shouldReceive('get')->with('files')->andReturn(null);
        Queue::fake();

        $result = app(StorageService::class)->getStorageData();
        $this->assertSame(['Error' => 'No data found.'], $result);
    }

    private function getFakeService(): BaseStorageService
    {
        return new class extends BaseStorageService
        {
            public function testProcess(array $response): array
            {
                $count = 0;
                $countBytes = 0;

                $this->countTotalObjectAndBytes($response, $count, $countBytes, 'bytes');
                return [$count, $countBytes];
            }

            public function testConvert(int $bytes): string
            {
                return $this->convertBytes($bytes);
            }
        };
    }
}
