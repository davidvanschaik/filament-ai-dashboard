<?php

namespace DavidvanSchaik\FilamentAiDashboard\Tests\Integration;

use DavidvanSchaik\FilamentAiDashboard\Clients\OpenAiClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OpenAiClientTest extends TestCase
{
    #[Test]
    public function it_validates_that_openai_usage_endpoint_returns_required_keys(): void
    {
        if (! config('filament-ai-dashboard-api.usage.key')) {
            $this->markTestSkipped('No API key found -> skipping real openAI API test');
        }

        $client = new OpenAiClient();

        $start = 1741219200;
        $end = 1741305600;

        $response = $client->getUsage($start, $end);

        $this->assertArrayHaskey('data', $response, 'Usage response missing "data" key');

        if (empty($response['data'])) {
            $this->markTestIncomplete('OpenAI returned no usage for this time range');
        }

        $bucket = null;

        foreach ($response['data'] as $item) {
            if (!empty($item['results'])) {
                $bucket = $item['results'][0];
                break;
            }
        }

        if ($bucket === null) {
            $this->assertNotNull($bucket, 'OpenAI returned usage data, but results buckets were empty');
        }

        $arrayKeys = ['num_model_requests', 'input_tokens', 'input_cached_tokens', 'output_tokens'];

        foreach ($arrayKeys as $key) {
            $this->assertArrayHasKey($key, $bucket, "OpenAI API contract changed → key '{$key}' missing.");
        }
    }

    #[Test]
    public function it_validates_that_openai_vector_store_endpoint_returns_required_keys(): void
    {
        if (! config('filament-ai-dashboard-api.storage.key')) {
            $this->markTestSkipped('No API key found -> skipping real openAI API test');
        }

        $client = new OpenAiClient();
        $response = $client->getVectorStores();

        $this->assertArrayHaskey('data', $response, 'Usage response missing "data" key');

        if (empty($response['data'])) {
            $this->markTestIncomplete('OpenAI returned no usage for this time range');
        }

        $object = $response['data'][0];

        if ($object === null) {
            $this->assertNotNull($object, 'OpenAI returned usage data, but results buckets were empty');
        }

        $this->assertArrayHasKey('usage_bytes', $object, 'No usage_byte array key found in object');
    }

    #[Test]
    public function it_validates_that_openai_files_endpoint_returns_required_keys(): void
    {
        if (! config('filament-ai-dashboard-api.storage.key')) {
            $this->markTestSkipped('No API key found -> skipping real openAI API test');
        }

        $client = new OpenAiClient();
        $response = $client->getFiles();

        $this->assertArrayHaskey('data', $response, 'Usage response missing "data" key');

        if (empty($response['data'])) {
            $this->markTestIncomplete('OpenAI returned no usage for this time range');
        }

        $object = $response['data'][0];

        if ($object === null) {
            $this->assertNotNull($object, 'OpenAI returned usage data, but results buckets were empty');
        }

        $this->assertArrayHasKey('bytes', $object, 'No usage_byte array key found in object');
    }
}
