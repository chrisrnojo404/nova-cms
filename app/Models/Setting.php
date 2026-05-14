<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

#[Fillable(['group', 'key', 'value', 'is_public', 'autoload'])]
class Setting extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'value' => AsArrayObject::class,
            'is_public' => 'boolean',
            'autoload' => 'boolean',
        ];
    }

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->value['value'] ?? $default;
    }

    public static function storeMany(array $settings): void
    {
        $payload = array_map(static function (array $setting): array {
            return [
                'group' => $setting['group'],
                'key' => $setting['key'],
                'value' => json_encode($setting['value']),
                'is_public' => $setting['is_public'] ?? false,
                'autoload' => $setting['autoload'] ?? true,
            ];
        }, $settings);

        static::upsert($payload, ['key'], ['group', 'value', 'is_public', 'autoload']);
    }
}
