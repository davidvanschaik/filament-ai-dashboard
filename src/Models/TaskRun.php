<?php

namespace DavidvanSchaik\FilamentAiDashboard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The TaskRun model tracks how many times each task in Baudex has been executed.
 * The message_id field keeps track of how many tokens each task used.
 * The duration field records how long each task took to complete.
 */
class TaskRun extends Model
{
    /** @use HasFactory<\Database\Factories\TaskRunFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'message_id',
        'duration',
        'created_at',
        'updated_at',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
