<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MediaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage media') ?? false;
    }

    public function rules(): array
    {
        return [
            'directory' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\/\-_]+$/'],
            'files' => ['required', 'array', 'min:1', 'max:12'],
            'files.*' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf,video/mp4', 'max:51200'],
        ];
    }
}
