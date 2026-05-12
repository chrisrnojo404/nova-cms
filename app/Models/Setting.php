<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
