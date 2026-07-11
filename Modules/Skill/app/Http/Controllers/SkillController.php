<?php

namespace Modules\Skill\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Modules\Skill\Http\Requests\IndexSkillRequest;
use Modules\Skill\Http\Requests\StoreSkillRequest;
use Modules\Skill\Http\Requests\UpdateSkillRequest;
use Modules\Skill\Services\SkillService;
use Modules\Skill\Transformers\SkillResource;

/**
 * @tags Skill
 */
class SkillController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected SkillService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return SkillResource::class;
    }

    protected function getModelName(): string
    {
        return 'skill';
    }

    /**
     * Display a listing of skills.
     */
    public function index(IndexSkillRequest $request): JsonResponse
    {
        $params = $request->validated();

        // Enforce active-only and return all for guests
        if (!auth('sanctum')->check()) {
            $params['is_active'] = true;
            unset($params['trashed']);
            if (!isset($params['per_page'])) {
                $params['per_page'] = -1;
            }
        }

        $skills = $this->service->index($params);

        return $this->paginatedResponse(
            SkillResource::collection($skills),
            'Skills retrieved successfully.'
        );
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(StoreSkillRequest $request): JsonResponse
    {
        $skill = $this->service->store($request->validated());

        return $this->resourceResponse(
            new SkillResource($skill),
            'Skill created successfully.',
            201
        );
    }

    /**
     * Display the specified skill.
     */
    public function show(string $id): JsonResponse
    {
        $withTrashed = auth('sanctum')->check();
        $skill = $this->service->findById($id, $withTrashed);

        if (!auth('sanctum')->check() && !$skill->is_active) {
            abort(404, 'Skill not found or inactive.');
        }

        return $this->resourceResponse(
            new SkillResource($skill),
            'Skill retrieved successfully.'
        );
    }

    /**
     * Update the specified skill in storage.
     */
    public function update(UpdateSkillRequest $request, string $id): JsonResponse
    {
        $skill = $this->service->findById($id);
        $updatedSkill = $this->service->update($skill, $request->validated());

        return $this->resourceResponse(
            new SkillResource($updatedSkill),
            'Skill updated successfully.'
        );
    }

    /**
     * Remove the specified skill from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $skill = $this->service->findById($id);
        $this->service->delete($skill);

        return $this->resourceResponse(
            new SkillResource($skill),
            'Skill deleted successfully.'
        );
    }

    /**
     * Toggle active status for the specified skill.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $skill = $this->service->findById($id);
        $updatedSkill = $this->service->toggleStatus($skill);

        return $this->resourceResponse(
            new SkillResource($updatedSkill),
            'Skill status updated successfully.'
        );
    }
}
