<?php

namespace DavidvanSchaik\FilamentAiDashboard\Services;

use Carbon\Carbon;

class TimeRangeService
{
    public function getThisMonthTimestamps(): array
    {
        $now = Carbon::now('UTC');

        $start = $now->copy()->startOfMonth()->startOfDay();
        $end = $now->copy()->endOfMonth()->endOfDay();

        return [$start->timestamp, $end->timestamp];
    }

    public function getMonthTimeStamps(Carbon $time): array
    {
        $start = $time->copy()->startOfMonth()->startOfDay()->timestamp;
        $end = $time->copy()->addMonthNoOverflow()->startOfMonth()->startOfDay()->timestamp;

        return [$start, $end];
    }

    public function parseToTimestamp(string $time): array
    {
        $time = Carbon::parse($time)->startOfMonth();

        return $this->getMonthTimeStamps($time);
    }

    public function parseToDate(int $time): string
    {
        $time = Carbon::parse($time)->format('Y-m-d');
        $time = explode('-', $time);

        return end($time);
    }

    public function daysInMonth(string $month): int
    {
        return Carbon::parse($month . '-01')->daysInMonth;
    }
}
