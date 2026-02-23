<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    /**
     * Upload a file and attach it to a User (acting as the generic uploader/owner).
     * The file is automatically converted to WebP via the User model's media conversions.
     */
    public function upload(User $user, UploadedFile $file, string $collection = 'default'): Media
    {
        return $user->addMedia($file)
            ->toMediaCollection($collection);
    }

    /**
     * Delete a media item.
     */
    public function delete(Media $media): void
    {
        $media->delete();
    }
}
