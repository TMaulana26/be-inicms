<?php

namespace App\Http\Controllers;

use App\Http\Requests\Media\StoreMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\MediaResource;

class MediaController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * Display a paginated listing of all media.
     * Useful for building a generic "Media Library" UI.
     */
    public function index(Request $request): JsonResponse
    {
        // Spatie's generic media model
        $mediaModel = config('media-library.media_model');
        $media = $mediaModel::latest()->paginate($request->get('per_page', 24));

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
            $request->input('collection', 'default')
        );

        return $this->resourceResponse(
            new MediaResource($media),
            'Media uploaded successfully.',
            201
        );
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroy(Media $media): JsonResponse
    {
        $this->mediaService->delete($media);

        return $this->successResponse(null, 'Media deleted successfully.');
    }
}
