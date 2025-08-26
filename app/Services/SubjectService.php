<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectService
{
    /**
     * @return LengthAwarePaginator<int, Subject>
     */
    public function paginate(?string $descricao = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Subject::query();
        if ($descricao) {
            $query->where('Descricao', 'like', '%'.$descricao.'%');
        }

        return $query->orderBy('Descricao')->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            return Subject::create(['Descricao' => $data['Descricao']]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Subject $subject, array $data): Subject
    {
        return DB::transaction(function () use ($subject, $data) {
            $subject->update(['Descricao' => $data['Descricao']]);
            return $subject;
        });
    }

    public function delete(Subject $subject): void
    {
        DB::transaction(function () use ($subject) {
            if ($subject->books()->count() > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assunto' => ['Não é possível excluir este assunto pois ele possui livros associados.']
                ]);
            }
            $subject->delete();
        });
    }
}
