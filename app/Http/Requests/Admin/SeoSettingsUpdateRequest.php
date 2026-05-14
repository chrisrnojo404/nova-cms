<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeoSettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage seo') ?? false;
    }

    public function rules(): array
    {
        return [
            'meta_title_template' => ['required', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['required', 'string', 'max:100'],
            'canonical_base_url' => ['required', 'url', 'max:255'],
            'og_site_name' => ['required', 'string', 'max:255'],
            'twitter_card' => ['required', Rule::in(['summary', 'summary_large_image'])],
            'robots_txt_content' => ['nullable', 'string', 'max:5000'],
            'sitemap_enabled' => ['nullable', 'boolean'],
        ];
    }
}
