<?php

namespace App\Repositories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository
{
    protected Subject $model;

    public function __construct(Subject $model)
    {
        $this->model = $model;
    }

    /**
     * Get all subjects with relationships
     */
    /**
     * @return Collection<int, Subject>
     */
    public function all(): Collection
    {
        return $this->model->with(['books'])->get();
    }

    /**
     * Find subject by ID
     */
    public function findById(int $id): ?Subject
    {
        return $this->model->with(['books'])->find($id);
    }

    /**
     * Create a new subject
     */
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Subject
    {
        return $this->model->create($data);
    }

    /**
     * Update a subject
     */
    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Subject $subject, array $data): bool
    {
        return $subject->update($data);
    }

    /**
     * Delete a subject
     */
    public function delete(Subject $subject): bool
    {
        return $subject->delete();
    }

    /**
     * Search subjects with filters and pagination
     */
    /**
     * Find subjects by description
     */
    /**
     * @return Collection<int, Subject>
     */
    public function findByDescription(string $description): Collection
    {
        return $this->model->with(['books'])
            ->where('Descricao', 'like', '%'.$description.'%')
            ->orderBy('Descricao')
            ->get();
    }

    /**
     * Count total subjects
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Get statistics
     */
    /**
     * @return array<string, int>
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'with_books' => $this->model->has('books')->count(),
            'without_books' => $this->model->doesntHave('books')->count(),
        ];
    }
}
