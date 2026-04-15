<?php

namespace Modules\Media\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Media\Http\Requests\Media\IndexMediaRequest;
use Modules\Media\Http\Requests\Media\StoreMediaRequest;
use Modules\Media\Http\Requests\Media\UpdateMediaRequest;
use Modules\Media\Models\Media;
use Modules\Media\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Media\Transformers\MediaResource;
use App\Traits\HandlesBulkAndSoftDeletes;

class MediaController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected MediaService $mediaService
    ) {}

    protected function getService()
    {
        return $this->mediaService;
    }

    protected function getResourceClass(): string
    {
        return MediaResource::class;
    }

    protected function getModelName(): string
    {
        return 'media';
    }

    /**
     * Display a paginated listing of all media.
     * Useful for building a generic "Media Library" UI.
     */
    public function index(IndexMediaRequest $request): JsonResponse
    {
        $media = $this->mediaService->index($request->all());

        return $this->paginatedResponse(
            MediaResource::collection($media),
            'Media retrieved successfully.'
        );
    }

    /**
     * Store a newly uploaded media file.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $user = $request->user();

        $media = $this->mediaService->upload(
            $user,
            $request->file('file'),
            $request->input('collection', 'default'),
            $request->input('name'),
            $request->input('category_id')
        );

        return $this->resourceResponse(
            new MediaResource($media),
            'Media uploaded successfully.',
            201
        );
    }

    /**
     * Display the specified media.
     */
    public function show(Media $media): JsonResponse
    {
        return $this->resourceResponse(
            new MediaResource($media),
            'Media details retrieved successfully.'
        );
    }

    /**
     * Update the specified media.
     */
    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        $media = $this->mediaService->update($media, $request->validated());

        return $this->resourceResponse(
            new MediaResource($media),
            'Media updated successfully.'
        );
    }

    /**
     * Remove the specified media from storage (Soft Delete).
     */
    public function destroy(Media $media): JsonResponse
    {
        $this->mediaService->delete($media);

        return $this->resourceResponse(new MediaResource($media), 'Media deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Media $media): JsonResponse
    {
        $media = $this->mediaService->toggleStatus($media);

        return $this->resourceResponse(
            new MediaResource($media),
            'Media status toggled successfully.'
        );
    }
}
