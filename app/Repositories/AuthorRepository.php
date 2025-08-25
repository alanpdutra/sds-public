<?php

namespace App\Repositories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;

class AuthorRepository
{
    protected Author $model;

    public function __construct(Author $model)
    {
        $this->model = $model;
    }

    /**
     * Get all authors with relationships
     */
    /**
     * @return Collection<int, Author>
     */
    public function all(): Collection
    {
        return $this->model->with(['books'])->get();
    }

    /**
     * Find author by ID
     */
    public function findById(int $id): ?Author
    {
        return $this->model->with(['books'])->find($id);
    }

    /**
     * Create a new author
     */
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Author
    {
        return $this->model->create($data);
    }

    /**
     * Update an author
     */
    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Author $author, array $data): bool
    {
        return $author->update($data);
    }

    /**
     * Delete an author
     */
    public function delete(Author $author): bool
    {
        return $author->delete();
    }

    /**
     * Search authors with filters and pagination
     */
    /**
     * Find authors by name
     */
    /**
     * @return Collection<int, Author>
     */
    public function findByName(string $name): Collection
    {
        return $this->model->with(['books'])
            ->where('Nome', 'like', '%'.$name.'%')
            ->orderBy('Nome')
            ->get();
    }

    /**
     * Count total authors
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
