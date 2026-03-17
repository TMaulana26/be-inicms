<?php

namespace App\Services;

use Modules\Acl\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\Media\Models\Media;
use Illuminate\Support\Facades\DB;

use App\Traits\HandlesIndexQuery;

class MediaService
{
    use HandlesIndexQuery;

    /**
     * Display a listing of media.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Media::query(),
            $params,
            ['name', 'file_name'],
            fn($q) => $q->when(($params['trashed'] ?? null) !== 'only' && ($params['trashed'] ?? null) !== 'with', function ($subQ) use ($params) {
                $subQ->where('collection_name', ($params['only_profile_picture'] ?? false) ? '=' : '!=', 'profile_picture');
            }),
            24
        );
    }

    /**
     * Upload a file and attach it to a User (acting as the generic uploader/owner).
     * The file is automatically converted to WebP via the User model's media conversions.
     */
    public function upload(User $user, UploadedFile $file, string $collection = 'default', ?string $name = null): Media
    {
        $media = $user->addMedia($file);

        if ($name) {
            $media->usingName($name);
        }

        $media = $media->toMediaCollection($collection);

        return $media;
    }

    /**
     * Update the specified media.
     */
    public function update(Media $media, array $data): Media
    {
        $media->update($data);
        return $media->refresh();
    }

    /**
     * Handle bulk operations for media.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => Media::query(),
                'restore',
                'forceDelete' => Media::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $mediaItems = $query->whereIn('id', $ids)->get();
            $foundIds = $mediaItems->pluck('id')->toArray();
            $failedIds = array_values(array_diff($ids, $foundIds));

            if ($mediaItems->isNotEmpty()) {
                match ($operation) {
                    'delete' => Media::whereIn('id', $foundIds)->delete(),
                    'restore' => Media::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => Media::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
                    'toggle' => $mediaItems->each(fn($m) => $m->update(['is_active' => !$m->is_active])),
                };

                if ($operation !== 'forceDelete') {
                    $mediaItems->each->refresh();
                }
            }

            return [
                'affected' => $mediaItems,
                'failed_ids' => $failedIds
            ];
        });
    }

    /**
     * Toggle the active status of a media item.
     */
    public function toggleStatus(Media $media): Media
    {
        $media->update(['is_active' => !$media->is_active]);
        return $media;
    }

    /**
     * Restore a soft-deleted media item.
     */
    public function restore(string $id): Media
    {
        return DB::transaction(function () use ($id) {
            $media = Media::onlyTrashed()->findOrFail($id);
            $media->restore();
            return $media->refresh();
        });
    }

    /**
     * Force delete a media item.
     */
    public function forceDelete(string $id): Media
    {
        return DB::transaction(function () use ($id) {
            $media = Media::onlyTrashed()->findOrFail($id);
            $mediaData = clone $media;
            $media->forceDelete();
            return $mediaData;
        });
    }

    /**
     * Delete a media item (Soft Delete if used via controller).
     */
    public function delete(Media $media): void
    {
        $media->delete();
    }
}
