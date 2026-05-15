<?php

namespace App\Support;

use App\Models\ContentRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class RevisionManager
{
    public function capture(Model $model, ?int $userId, string $label): ContentRevision
    {
        return $model->revisions()->create([
            'user_id' => $userId,
            'label' => $label,
            'snapshot' => $this->snapshot($model),
        ]);
    }

    public function restore(Model $model, ContentRevision $revision): void
    {
        $snapshot = is_array($revision->snapshot) ? $revision->snapshot : $revision->snapshot?->getArrayCopy();
        $payload = Arr::only($snapshot ?? [], $this->restorableFields($model));

        $model->forceFill($payload)->save();
    }

    private function snapshot(Model $model): array
    {
        return Arr::only($model->getAttributes(), $this->restorableFields($model));
    }

    private function restorableFields(Model $model): array
    {
        return array_values(array_filter($model->getFillable(), static fn (string $field): bool => $field !== 'author_id'));
    }
}
