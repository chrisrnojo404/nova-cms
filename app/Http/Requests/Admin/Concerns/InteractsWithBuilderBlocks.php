<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Support\BlockBuilder;
use Illuminate\Validation\Validator;
use JsonException;

trait InteractsWithBuilderBlocks
{
    protected function validateBuilderBlocks(Validator $validator): void
    {
        $raw = trim((string) $this->input('builder_blocks', ''));

        if ($raw === '') {
            return;
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $validator->errors()->add('builder_blocks', 'Builder blocks must be valid JSON.');
            return;
        }

        if (! is_array($decoded)) {
            $validator->errors()->add('builder_blocks', 'Builder blocks must decode to an array of block objects.');
            return;
        }

        $builder = app(BlockBuilder::class);

        foreach ($builder->validationErrors($decoded) as $error) {
            $validator->errors()->add('builder_blocks', $error);
        }
    }
}
