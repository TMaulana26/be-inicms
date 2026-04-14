<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Modules\Blog\Http\Requests\IndexPostRequest;
use Modules\Blog\Http\Requests\PostRequest;
use Modules\Blog\Services\PostService;
use Modules\Blog\Transformers\PostResource;

class PostController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected PostService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return PostResource::class;
    }

    protected function getModelName(): string
    {
        return 'post';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexPostRequest $request): JsonResponse
    {
        $posts = $this->service->getPosts($request->validated());

        return PostResource::collection($posts)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $post = $this->service->createPost($request->validated());

        return $this->resourceResponse(
            new PostResource($post->load(['category', 'author'])),
            'Post created successfully.',
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $post = $this->service->findById($id, true);

        return $this->resourceResponse(
            new PostResource($post->load(['category', 'author']))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id): JsonResponse
    {
        $post = $this->service->findById($id);
        $updatedPost = $this->service->updatePost($post, $request->validated());

        return $this->resourceResponse(
            new PostResource($updatedPost->load(['category', 'author'])),
            'Post updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $post = $this->service->findById($id);
        $this->service->deletePost($post);

        return $this->resourceResponse(
            new PostResource($post),
            'Post deleted successfully.'
        );
    }
}
