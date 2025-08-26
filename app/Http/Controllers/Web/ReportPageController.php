<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportPageController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function pdf(): Response
    {
        $driver = DB::getDriverName();
        $exists = false;
        if ($driver === 'sqlite') {
            $exists = DB::selectOne("SELECT name FROM sqlite_master WHERE type='view' AND name='vw_relatorio_autor'") !== null;
        } else {
            try {
                $exists = DB::selectOne("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_".DB::getDatabaseName()." = 'vw_relatorio_autor'") !== null;
            } catch (\Throwable $e) {
                $exists = false;
            }
        }
        if (! $exists) {
            DB::statement('DROP VIEW IF EXISTS vw_relatorio_autor');
            if ($driver === 'sqlite') {
                DB::statement(<<<'SQL'
                    CREATE VIEW vw_relatorio_autor AS
                    SELECT
                      a.Nome AS AutorNome,
                      l.Titulo AS LivroTitulo,
                      l.Valor AS LivroValor,
                      GROUP_CONCAT(s.Descricao, ', ') AS AssuntosConcatenados,
                      (
                        SELECT COUNT(DISTINCT l2.CodL)
                        FROM Livro l2
                        JOIN Livro_Autor la2 ON la2.Livro_CodL = l2.CodL
                        WHERE la2.Autor_CodAu = a.CodAu
                      ) AS QuantidadeLivrosPorAutor
                    FROM Autor a
                    JOIN Livro_Autor la ON la.Autor_CodAu = a.CodAu
                    JOIN Livro l ON l.CodL = la.Livro_CodL
                    LEFT JOIN Livro_Assunto ls ON ls.Livro_CodL = l.CodL
                    LEFT JOIN Assunto s ON s.CodAs = ls.Assunto_CodAs
                    GROUP BY a.CodAu, a.Nome, l.CodL, l.Titulo, l.Valor
                SQL);
            } else {
                DB::statement(<<<'SQL'
                    CREATE VIEW vw_relatorio_autor AS
                    SELECT
                      a.Nome AS AutorNome,
                      l.Titulo AS LivroTitulo,
                      l.Valor AS LivroValor,
                      GROUP_CONCAT(DISTINCT s.Descricao ORDER BY s.Descricao SEPARATOR ', ') AS AssuntosConcatenados,
                      COUNT(DISTINCT l.CodL) OVER (PARTITION BY a.CodAu) AS QuantidadeLivrosPorAutor
                    FROM Autor a
                    JOIN Livro_Autor la ON la.Autor_CodAu = a.CodAu
                    JOIN Livro l ON l.CodL = la.Livro_CodL
                    LEFT JOIN Livro_Assunto ls ON ls.Livro_CodL = l.CodL
                    LEFT JOIN Assunto s ON s.CodAs = ls.Assunto_CodAs
                    GROUP BY a.CodAu, a.Nome, l.CodL, l.Titulo, l.Valor
                    ORDER BY a.Nome, l.Titulo
                SQL);
            }
        }

        $rows = DB::table('vw_relatorio_autor')->get();
        $grouped = [];
        $grandTotal = 0.0;
        foreach ($rows as $r) {
            $autor = (string) $r->AutorNome;
            $grouped[$autor] ??= [
                'books' => [],
                'total' => 0.0,
            ];
            $grouped[$autor]['books'][] = [
                'titulo' => (string) $r->LivroTitulo,
                'valor' => (float) ($r->LivroValor ?? 0),
                'assuntos' => (string) ($r->AssuntosConcatenados ?? ''),
            ];
            $grouped[$autor]['total'] += (float) ($r->LivroValor ?? 0);
            $grandTotal += (float) ($r->LivroValor ?? 0);
        }

        ksort($grouped);
        $authors = $grouped;
        $generatedAt = now()->format('d/m/Y H:i');

        $html = view('reports.pdf', compact('authors', 'generatedAt', 'grandTotal'))->render();
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('a4');
        $dompdf->render();
        $output = $dompdf->output();

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="relatorio-livros-por-autor.pdf"',
        ]);
    }
}
