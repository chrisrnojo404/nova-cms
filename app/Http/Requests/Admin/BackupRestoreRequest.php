<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BackupRestoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'backup_snapshot' => ['required', 'file', 'mimes:json,zip', 'max:20480'],
            'confirmation' => ['accepted'],
            'create_safety_backup' => ['nullable', 'boolean'],
        ];
    }
}
