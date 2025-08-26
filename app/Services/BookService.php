<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService
{
    public function paginate(?string $titulo = null, ?string $autor = null, ?string $assunto = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Book::with(['authors', 'subjects']);

        if ($titulo) {
            $query->where('Titulo', 'like', '%' . $titulo . '%');
        }

        if ($autor) {
            $query->whereHas('authors', function ($q) use ($autor) {
                $q->where('Nome', 'like', '%' . $autor . '%');
            });
        }

        if ($assunto) {
            $query->whereHas('subjects', function ($q) use ($assunto) {
                $q->where('Descricao', 'like', '%' . $assunto . '%');
            });
        }

        return $query->orderBy('Titulo')->paginate($perPage);
    }

    public function create(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            $book = Book::create([
                'Titulo' => $data['Titulo'],
                'Editora' => $data['Editora'],
                'Edicao' => $data['Edicao'],
                'AnoPublicacao' => $data['AnoPublicacao'],
                'Valor' => $data['Valor'] ?? 0,
            ]);

            if (!empty($data['authors'])) {
                $book->authors()->sync($data['authors']);
            }

            if (!empty($data['subjects'])) {
                $book->subjects()->sync($data['subjects']);
            }

            return $book->load(['authors', 'subjects']);
        });
    }

    public function update(Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data) {
            $book->update([
                'Titulo' => $data['Titulo'] ?? $book->Titulo,
                'Editora' => $data['Editora'] ?? $book->Editora,
                'Edicao' => $data['Edicao'] ?? $book->Edicao,
                'AnoPublicacao' => $data['AnoPublicacao'] ?? $book->AnoPublicacao,
                'Valor' => array_key_exists('Valor', $data) ? $data['Valor'] : $book->Valor,
            ]);

            if (array_key_exists('authors', $data)) {
                $book->authors()->sync($data['authors'] ?? []);
            }

            if (array_key_exists('subjects', $data)) {
                $book->subjects()->sync($data['subjects'] ?? []);
            }

            return $book->load(['authors', 'subjects']);
        });
    }

    public function delete(Book $book): void
    {
        DB::transaction(function () use ($book) {
            $book->authors()->detach();
            $book->subjects()->detach();
            $book->delete();
        });
    }

    public function findOrFail(int $id): Book
    {
        return Book::with(['authors', 'subjects'])->findOrFail($id);
    }

    public function statsSummary(): array
    {
        return [
            'booksTotal' => Book::count(),
            'authorsTotal' => Author::count(),
            'subjectsTotal' => Subject::count(),
            'latestBooks' => Book::orderByDesc('CodL')
                ->limit(5)
                ->get(['CodL', 'Titulo', 'AnoPublicacao'])
                ->toArray(),
            'updatedAt' => now()->toISOString(),
        ];
    }
}
