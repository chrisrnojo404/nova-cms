<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:500'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'active_theme' => ['required', 'string', 'max:255'],
            'brand_accent' => ['required', 'string', 'max:20'],
            'homepage_mode' => ['required', Rule::in(['preview', 'page'])],
            'homepage_page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'posts_per_page' => ['required', 'integer', 'min:1', 'max:24'],
            'media_upload_directory' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\/\-_]+$/'],
            'image_quality' => ['required', 'integer', 'min:20', 'max:100'],
            'backup_retention_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ];
    }
}
