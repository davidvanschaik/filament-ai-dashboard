<?php

namespace DavidvanSchaik\FilamentAiDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'name',
    ];

    public function taskRuns(): HasMany
    {
        return $this->hasMany(TaskRun::class);
    }
}
