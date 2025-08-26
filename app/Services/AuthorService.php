<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    /**
     * @return LengthAwarePaginator<int, Author>
     */
    public function paginate(?string $nome = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Author::query();
        if ($nome) {
            $query->where('Nome', 'like', '%'.$nome.'%');
        }

        return $query->orderBy('Nome')->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Author
    {
        return DB::transaction(function () use ($data) {
            return Author::create(['Nome' => $data['Nome']]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Author $author, array $data): Author
    {
        return DB::transaction(function () use ($author, $data) {
            $author->update(['Nome' => $data['Nome']]);
            return $author;
        });
    }

    public function delete(Author $author): void
    {
        DB::transaction(function () use ($author) {
            if ($author->books()->count() > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'autor' => ['Não é possível excluir este autor pois ele possui livros associados.']
                ]);
            }
            $author->delete();
        });
    }
}
