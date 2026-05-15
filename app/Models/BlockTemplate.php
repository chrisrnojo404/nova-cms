<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'slug',
    'scope',
    'description',
    'blocks',
    'is_active',
])]
class BlockTemplate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForContext($query, string $context)
    {
        return $query
            ->where('is_active', true)
            ->whereIn('scope', [$context, 'both']);
    }
}
