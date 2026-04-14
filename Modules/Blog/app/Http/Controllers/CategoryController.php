<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Modules\Blog\Http\Requests\CategoryRequest;
use Modules\Blog\Http\Requests\IndexCategoryRequest;
use Modules\Blog\Services\CategoryService;
use Modules\Blog\Transformers\CategoryResource;

class CategoryController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected CategoryService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return CategoryResource::class;
    }

    protected function getModelName(): string
    {
        return 'category';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCategoryRequest $request): JsonResponse
    {
        $categories = $this->service->getCategories($request->validated());

        return CategoryResource::collection($categories)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->service->createCategory($request->validated());

        return $this->resourceResponse(
            new CategoryResource($category),
            'Category created successfully.',
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = $this->service->findById($id, true);

        return $this->resourceResponse(
            new CategoryResource($category->loadCount('posts'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->service->findById($id);
        $updatedCategory = $this->service->updateCategory($category, $request->validated());

        return $this->resourceResponse(
            new CategoryResource($updatedCategory),
            'Category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = $this->service->findById($id);
        $this->service->deleteCategory($category);

        return $this->resourceResponse(
            new CategoryResource($category),
            'Category deleted successfully.'
        );
    }

    /**
     * Toggle status for the specified resource.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $category = $this->service->findById($id);
        $updatedCategory = $this->service->toggleStatus($category);

        return $this->resourceResponse(
            new CategoryResource($updatedCategory),
            'Category status updated successfully.'
        );
    }
}
