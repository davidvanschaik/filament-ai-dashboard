<?php

namespace DavidvanSchaik\FilamentAiDashboard\Repositories;

use DavidvanSchaik\FilamentAiDashboard\Models\TaskRun;

class TaskRunRepository
{
    public function getExecutedJobs(string $start = '', string $end = ''): array
    {
        $query = TaskRun::with('task');

        if (! empty($start) && ! empty($end)) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->get()
            ->groupBy('task_id')
            ->mapWithKeys(function ($taskRuns) {
                $task = $taskRuns->first()->task;

                return [
                    $task->name => [
                        'execution_count' => $taskRuns->count(),
                        'message_ids' => $taskRuns->pluck('message_id')->toArray(),
                        'total_duration' => $taskRuns->sum('duration'),
                    ]
                ];
            })
            ->toArray();
    }
}
