<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\InteractsWithBuilderBlocks;
use App\Models\BlockTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlockTemplateUpdateRequest extends FormRequest
{
    use InteractsWithBuilderBlocks;

    public function authorize(): bool
    {
        return $this->user()?->can('manage pages') || $this->user()?->can('manage posts');
    }

    public function rules(): array
    {
        /** @var BlockTemplate $template */
        $template = $this->route('block_template');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('block_templates', 'slug')->ignore($template)],
            'scope' => ['required', Rule::in(['page', 'post', 'both'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'builder_blocks' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(fn ($validator) => $this->validateBuilderBlocks($validator));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name')),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
