<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlockTemplateImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage pages') || $this->user()?->can('manage posts');
    }

    public function rules(): array
    {
        return [
            'template_json' => ['nullable', 'string'],
            'template_file' => ['nullable', 'file', 'mimes:json,txt', 'max:1024'],
        ];
    }
}
