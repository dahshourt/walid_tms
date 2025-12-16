<?php

namespace App\Http\Repository\Project;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProjectRepository
{
    public function getAll(): LengthAwarePaginator
    {
        return Project::with(['quarters' => function ($query) {
            $query->whereNull('deleted_at');
        }, 'quarters.milestones' => function ($query) {
            $query->whereNull('deleted_at');
        }])
        ->orderBy('id', 'desc')
        ->paginate(15);
    }

    /**
     * Return all projects (lightweight) for dropdowns/selects.
     */
    public function listAll(): Collection
    {
        return Project::orderBy('name')->get(['id', 'name']);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(array $data, int $id): bool
    {
        return Project::where('id', $id)->update($data);
    }

    public function find(int $id): ?Project
    {
        return Project::with(['quarters' => function ($query) {
            $query->whereNull('deleted_at');
        }, 'quarters.milestones' => function ($query) {
            $query->whereNull('deleted_at');
        }])->find($id);
    }

    public function delete(int $id): bool
    {
        $project = $this->find($id);

        if (!$project) {
            return false;
        }

        return $project->delete();
    }
}


