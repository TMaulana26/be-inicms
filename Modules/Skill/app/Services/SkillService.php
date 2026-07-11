<?php

namespace Modules\Skill\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Facades\DB;
use Modules\Skill\Models\Skill;

class SkillService
{
    use HandlesIndexQuery;

    /**
     * Find a skill by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): Skill
    {
        $query = Skill::query();
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * Display a listing of skills.
     */
    public function index(array $params)
    {
        if (empty($params['sort_by'])) {
            $params['sort_by'] = 'order';
        }

        return $this->handleIndexQuery(
            Skill::query(),
            $params,
            ['name', 'category'],
            function ($query) use ($params) {
                if (isset($params['is_active'])) {
                    $query->where('is_active', filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN));
                }
            },
            15
        );
    }

    /**
     * Create a new skill.
     */
    public function store(array $data): Skill
    {
        return DB::transaction(function () use ($data) {
            return Skill::create($data);
        });
    }

    /**
     * Update an existing skill.
     */
    public function update(Skill $skill, array $data): Skill
    {
        return DB::transaction(function () use ($skill, $data) {
            $skill->update($data);
            return $skill->refresh();
        });
    }

    /**
     * Soft delete a skill.
     */
    public function delete(Skill $skill): bool
    {
        return $skill->delete();
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Skill $skill): Skill
    {
        return DB::transaction(function () use ($skill) {
            $skill->update(['is_active' => !$skill->is_active]);
            return $skill->refresh();
        });
    }

    /**
     * Restore a deleted skill.
     */
    public function restore(string $id): Skill
    {
        return DB::transaction(function () use ($id) {
            $skill = Skill::onlyTrashed()->findOrFail($id);
            $skill->restore();
            return $skill->refresh();
        });
    }

    /**
     * Force delete a skill.
     */
    public function forceDelete(string $id): Skill
    {
        return DB::transaction(function () use ($id) {
            $skill = Skill::onlyTrashed()->findOrFail($id);
            $skillData = clone $skill;
            $skill->forceDelete();
            return $skillData;
        });
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => Skill::query(),
                'restore',
                'forceDelete' => Skill::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $skills = $query->whereIn('id', $ids)->get();
            $foundIds = $skills->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($skills->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        Skill::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        Skill::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        foreach ($skills as $skill) {
                            $skill->forceDelete();
                        }
                        break;
                    case 'toggle':
                        foreach ($skills as $skill) {
                            $skill->update(['is_active' => !$skill->is_active]);
                        }
                        break;
                }

                if ($operation !== 'forceDelete') {
                    $skills = Skill::withTrashed()->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $skills,
                'failed_ids' => $notFoundIds,
            ];
        });
    }
}
