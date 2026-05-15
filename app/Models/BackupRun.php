<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'initiated_by',
    'status',
    'queue_connection',
    'artifact_disk',
    'artifact_path',
    'artifact_size',
    'summary',
    'error_message',
    'started_at',
    'completed_at',
])]
class BackupRun extends Model
{
    protected function casts(): array
    {
        return [
            'summary' => AsArrayObject::class,
            'artifact_size' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
