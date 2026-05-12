<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'event',
    'subject_type',
    'subject_id',
    'description',
    'properties',
    'ip_address',
    'user_agent',
])]
class ActivityLog extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'properties' => AsArrayObject::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
