<?php

namespace DavidvanSchaik\FilamentAiDashboard\Repositories;

use DavidvanSchaik\FilamentAiDashboard\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
    // Builds array with daily activity for Token Euro Chart
    public function getMessagesFromMonth(string $start, string $end): array
    {
        $messages = Message::with('project')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('metadata->model')
            ->get();

        $grouped = [];

        foreach ($messages as $message) {
            $day = $message->created_at->format('d');
            $project = $message->project->name;
            $meta = json_decode($message->metadata);
            $model = $meta->model;

            if (!isset($grouped[$day][$project][$model])) {
                $grouped[$day][$project][$model] = [
                    'day' => $day,
                    'project' => $project,
                    'model' => $model,
                    'input_tokens' => 0,
                    'cached_tokens' => 0,
                    'output_tokens' => 0,
                ];
            }

            $grouped[$day][$project][$model]['input_tokens'] += $message->input_tokens;
            $grouped[$day][$project][$model]['cached_tokens'] += $message->input_cached_tokens;
            $grouped[$day][$project][$model]['output_tokens'] += $message->output_tokens;
        }

        $result = [];
        foreach ($grouped as $groupedDay) {
            foreach ($groupedDay as $groupedProject) {
                foreach ($groupedProject as $groupedModel) {
                    $result[] = $groupedModel;
                }
            }
        }

        usort($result, fn($a, $b) => $a['day'] <=> $b['day']);

        return $result;
    }

    public function findMany(array $ids): Collection
    {
        return Message::whereIn('id', $ids)->get();
    }
}
