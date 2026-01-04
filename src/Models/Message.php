<?php

namespace DavidvanSchaik\FilamentAiDashboard\Models;

use DavidvanSchaik\FilamentAiDashboard\Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'metadata',
        'type',
        'input_tokens',
        'input_cached_tokens',
        'output_tokens',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function taskRun(): HasOne
    {
        return $this->hasOne(TaskRun::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): MessageFactory
    {
        return MessageFactory::new();
    }
}
