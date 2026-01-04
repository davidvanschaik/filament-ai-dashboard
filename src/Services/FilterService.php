<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

class FilterService
{
    // Return the top $limit entries ordered by value (highest first).
    public function sortData(array $data, int $limit): array
    {
        asort($data);

        return array_slice(array_reverse($data), 0, $limit, true);
    }
}
