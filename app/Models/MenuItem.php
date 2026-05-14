<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'menu_id',
    'parent_id',
    'linked_type',
    'linked_id',
    'title',
    'url',
    'target',
    'position',
    'is_active',
])]
class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = [
        'resolved_title',
        'resolved_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('position');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'linked_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'linked_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'linked_id');
    }

    public function getResolvedTitleAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        return match ($this->linked_type) {
            'page' => $this->page?->title ?? 'Page',
            'post' => $this->post?->title ?? 'Post',
            'category' => $this->category?->name ?? 'Category',
            default => 'Menu item',
        };
    }

    public function getResolvedUrlAttribute(): string
    {
        return match ($this->linked_type) {
            'page' => $this->page?->slug ? route('pages.show', $this->page->slug) : ($this->url ?: '#'),
            'post' => $this->post?->slug ? route('posts.show', $this->post->slug) : ($this->url ?: '#'),
            'category' => $this->category?->slug ? route('posts.category', $this->category->slug) : ($this->url ?: '#'),
            default => $this->url ?: '#',
        };
    }
}
