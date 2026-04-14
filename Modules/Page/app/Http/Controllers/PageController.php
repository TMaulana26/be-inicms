<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Modules\Page\Http\Requests\IndexPageRequest;
use Modules\Page\Http\Requests\PageRequest;
use Modules\Page\Services\PageService;
use Modules\Page\Transformers\PageResource;

class PageController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected PageService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return PageResource::class;
    }

    protected function getModelName(): string
    {
        return 'page';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexPageRequest $request): JsonResponse
    {
        $pages = $this->service->getPages($request->validated());

        return PageResource::collection($pages)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PageRequest $request): JsonResponse
    {
        $page = $this->service->createPage($request->validated());

        return $this->resourceResponse(
            new PageResource($page->load('author')),
            'Page created successfully.',
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $page = $this->service->findById($id, true);

        return $this->resourceResponse(
            new PageResource($page->load('author'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PageRequest $request, string $id): JsonResponse
    {
        $page = $this->service->findById($id);
        $updatedPage = $this->service->updatePage($page, $request->validated());

        return $this->resourceResponse(
            new PageResource($updatedPage->load('author')),
            'Page updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $page = $this->service->findById($id);
        $this->service->deletePage($page);

        return $this->resourceResponse(
            new PageResource($page),
            'Page deleted successfully.'
        );
    }
}
