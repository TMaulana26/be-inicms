<?php

namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Modules\Portfolio\Http\Requests\IndexProjectRequest;
use Modules\Portfolio\Http\Requests\StoreProjectRequest;
use Modules\Portfolio\Http\Requests\UpdateProjectRequest;
use Modules\Portfolio\Services\PortfolioService;
use Modules\Portfolio\Transformers\ProjectResource;

/**
 * @tags Portfolio
 */
class ProjectController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected PortfolioService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return ProjectResource::class;
    }

    protected function getModelName(): string
    {
        return 'project';
    }

    /**
     * Display a listing of projects.
     */
    public function index(IndexProjectRequest $request): JsonResponse
    {
        $params = $request->validated();

        // Enforce active-only for guests (public access)
        if (!auth('sanctum')->check()) {
            $params['is_active'] = true;
            unset($params['trashed']);
        }

        $projects = $this->service->index($params);

        return $this->paginatedResponse(
            ProjectResource::collection($projects),
            'Projects retrieved successfully.'
        );
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']['en'] ?? '');
        }

        $project = $this->service->store($data);

        return $this->resourceResponse(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    /**
     * Display the specified project.
     */
    public function show(string $id): JsonResponse
    {
        // Guests can only see active projects; authenticated users can see soft-deleted ones as well
        $withTrashed = auth('sanctum')->check();
        $project = $this->service->findById($id, $withTrashed);

        if (!auth('sanctum')->check() && !$project->is_active) {
            abort(404, 'Project not found or inactive.');
        }

        return $this->resourceResponse(
            new ProjectResource($project),
            'Project retrieved successfully.'
        );
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, string $id): JsonResponse
    {
        $project = $this->service->findById($id);
        $updatedProject = $this->service->update($project, $request->validated());

        return $this->resourceResponse(
            new ProjectResource($updatedProject),
            'Project updated successfully.'
        );
    }

    /**
     * Remove the specified project from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $project = $this->service->findById($id);
        $this->service->delete($project);

        return $this->resourceResponse(
            new ProjectResource($project),
            'Project deleted successfully.'
        );
    }

    /**
     * Toggle active status for the specified project.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $project = $this->service->findById($id);
        $updatedProject = $this->service->toggleStatus($project);

        return $this->resourceResponse(
            new ProjectResource($updatedProject),
            'Project status updated successfully.'
        );
    }
}
