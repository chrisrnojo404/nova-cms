<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage menus') ?? false;
    }

    public function rules(): array
    {
        $menu = $this->route('menu');

        return [
            'parent_id' => [
                'nullable',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('menu_id', $menu instanceof Menu ? $menu->id : 0)),
            ],
            'linked_type' => ['required', Rule::in(['page', 'post', 'category', 'custom'])],
            'linked_id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'target' => ['required', Rule::in(['same_tab', 'new_tab'])],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $linkedType = $this->input('linked_type');
            $linkedId = $this->input('linked_id');

            if ($linkedType === 'custom') {
                if (! $this->filled('title')) {
                    $validator->errors()->add('title', 'A title is required for custom links.');
                }

                if (! $this->filled('url')) {
                    $validator->errors()->add('url', 'A URL is required for custom links.');
                }

                return;
            }

            if (! $linkedId) {
                $validator->errors()->add('linked_id', 'Select a source record for this menu item.');
                return;
            }

            $exists = match ($linkedType) {
                'page' => \App\Models\Page::query()->whereKey($linkedId)->exists(),
                'post' => \App\Models\Post::query()->whereKey($linkedId)->exists(),
                'category' => \App\Models\Category::query()->whereKey($linkedId)->exists(),
                default => false,
            };

            if (! $exists) {
                $validator->errors()->add('linked_id', 'The selected source record could not be found.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'position' => $this->input('position', 0),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
