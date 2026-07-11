<?php

namespace Modules\Portfolio\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Facades\DB;
use Modules\Portfolio\Models\Project;

class PortfolioService
{
    use HandlesIndexQuery;

    /**
     * Find a project by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): Project
    {
        $query = Project::query();
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * Display a listing of projects.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Project::query(),
            $params,
            ['title', 'slug', 'category', 'description', 'tech_stack'],
            function ($query) use ($params) {
                if (isset($params['is_active'])) {
                    $query->where('is_active', filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN));
                }
            },
            15
        );
    }

    /**
     * Create a new project.
     */
    public function store(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create($data);

            if (isset($data['screenshot'])) {
                $project->addMedia($data['screenshot'])->toMediaCollection('screenshot');
            }

            return $project->refresh();
        });
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update($data);

            if (isset($data['screenshot'])) {
                $project->addMedia($data['screenshot'])->toMediaCollection('screenshot');
            }

            return $project->refresh();
        });
    }

    /**
     * Soft delete a project.
     */
    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Project $project): Project
    {
        return DB::transaction(function () use ($project) {
            $project->update(['is_active' => !$project->is_active]);

            return $project->refresh();
        });
    }

    /**
     * Restore a deleted project.
     */
    public function restore(string $id): Project
    {
        return DB::transaction(function () use ($id) {
            $project = Project::onlyTrashed()->findOrFail($id);
            $project->restore();

            return $project->refresh();
        });
    }

    /**
     * Force delete a project.
     */
    public function forceDelete(string $id): Project
    {
        return DB::transaction(function () use ($id) {
            $project = Project::onlyTrashed()->findOrFail($id);
            $projectData = clone $project;
            
            // Clear media from disk/DB via Spatie
            $project->clearMediaCollection('screenshot');
            $project->forceDelete();

            return $projectData;
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
                'toggle' => Project::query(),
                'restore',
                'forceDelete' => Project::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $projects = $query->whereIn('id', $ids)->get();
            $foundIds = $projects->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($projects->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        Project::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        Project::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        foreach ($projects as $project) {
                            $project->clearMediaCollection('screenshot');
                            $project->forceDelete();
                        }
                        break;
                    case 'toggle':
                        foreach ($projects as $project) {
                            $project->update(['is_active' => !$project->is_active]);
                        }
                        break;
                }

                if ($operation !== 'forceDelete') {
                    $projects = Project::withTrashed()->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $projects,
                'failed_ids' => $notFoundIds,
            ];
        });
    }
}
