<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $booksTotal = (int) DB::table('Livro')->count();
        $authorsTotal = (int) DB::table('Autor')->count();
        $subjectsTotal = (int) DB::table('Assunto')->count();

        try {
            $rows = DB::table('vw_relatorio_autor')
                ->join('Livro as l', 'l.Titulo', '=', 'vw_relatorio_autor.LivroTitulo')
                ->select('l.CodL', 'vw_relatorio_autor.LivroTitulo as Titulo', 'l.AnoPublicacao', 'l.Editora', 'vw_relatorio_autor.LivroValor as Valor', 'vw_relatorio_autor.AutorNome')
                ->orderByDesc('l.CodL')
                ->get();
        } catch (\Throwable $e) {
            $rows = DB::table('Livro as l')
                ->join('Livro_Autor as la', 'la.Livro_CodL', '=', 'l.CodL')
                ->join('Autor as a', 'a.CodAu', '=', 'la.Autor_CodAu')
                ->select('l.CodL as CodL', 'l.Titulo', 'l.AnoPublicacao', 'l.Editora', 'l.Valor', 'a.Nome as AutorNome')
                ->orderByDesc('l.CodL')
                ->get();
        }

        $seen = [];
        $latest = [];
        foreach ($rows as $row) {
            if (isset($seen[$row->CodL])) {
                continue;
            }
            $seen[$row->CodL] = true;
            $latest[] = [
                'CodL' => (int) $row->CodL,
                'Titulo' => (string) $row->Titulo,
                'AnoPublicacao' => (int) $row->AnoPublicacao,
                'Editora' => $row->Editora !== null ? (string) $row->Editora : null,
                'Valor' => $row->Valor !== null ? (float) $row->Valor : null,
                'Autor' => (string) $row->AutorNome,
            ];
            if (count($latest) >= 5) {
                break;
            }
        }

        return [
            'booksTotal' => $booksTotal,
            'authorsTotal' => $authorsTotal,
            'subjectsTotal' => $subjectsTotal,
            'latestBooks' => $latest,
            'updatedAt' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
