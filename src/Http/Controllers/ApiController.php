<?php

namespace DavidvanSchaik\FilamentAiDashboard\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiController extends Controller
{
    private array $models = [];

    public function getModelsData(Request $request): JsonResponse
    {
        try {
            $start = $request->query('start_time');
            $end = $request->query('end_time');

            $path = storage_path('data/models.json');

            if (! file_exists($path)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $models = json_decode(file_get_contents($path), true);


            foreach ($models['data'] as $model) {
                $this->filterData($model, $start, $end);
            }

            return response()->json([
                "object" => "page",
                "has_more" => null,
                "next_page" => null,
                "data" => $this->models
            ]);

        } catch (Throwable $e) {
            Log::error('ApiController: Unexpected error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server error'], 500);
        }
    }

    public function getStorageData(int $limit, ?string $after, string $path): JsonResponse
    {
        $json = json_decode(file_get_contents($path), true);
        $vectors = $json['data'];

        if ($after === null || $after === '') {
            $startIndex = 0;
        } else {
            $startIndex = array_search($after, array_column($vectors, 'id'));

            if ($startIndex === false) {
                $startIndex = 0;
            } else {
                $startIndex += 1;
            }
        }

        $data = array_slice($vectors, $startIndex, $limit);

        if (empty($data)) {
            return response()->json([
                "object" => "list",
                "data" => [],
                "first_id" => null,
                "last_id" => null,
                "has_more" => false
            ]);
        }

        $firstId = $data[0]['id'];
        $lastId = $data[count($data) - 1]['id'];
        $allTimeLastId = $vectors[count($vectors) - 1]['id'];
        $hasMore = ($lastId !== $allTimeLastId);

        return response()->json([
            "object"   => "list",
            "data"     => $data,
            "first_id" => $firstId,
            "last_id"  => $lastId,
            "has_more" => $hasMore,
        ]);
    }

    public function getFiles(Request $request): JsonResponse
    {
        $path = storage_path('data/files.json');
        $limit = $request->query('limit');
        $after = $request->query('after');

        return $this->getStorageData($limit, $after, $path);
    }

    public function getVectorStores(Request $request): JsonResponse
    {
        $path = storage_path('data/vector_stores.json');
        $limit = $request->query('limit');
        $after = $request->query('after');

        return $this->getStorageData($limit, $after, $path);
    }

    private function filterData(array $model, int $startTime, int $endTime): void
    {
        $day = 86400;
        $start = $model['start_time'];
        $end = $model['end_time'];

        if ($end - $day <= $endTime && $start >= $startTime) {
            $this->models[] = $model;
        }
    }
}
