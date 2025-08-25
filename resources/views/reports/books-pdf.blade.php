<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Livros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #007bff;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        tr:hover {
            background-color: #e8f4f8;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .no-books {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .authors, .subjects {
            font-size: 11px;
        }
        
        .value {
            font-weight: bold;
            color: #28a745;
        }
        
        @media print {
            body {
                margin: 0;
            }
            
            .header {
                page-break-after: avoid;
            }
            
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Livros</h1>
        <p>Sistema de Gerenciamento de Biblioteca</p>
        <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    @if($books->count() > 0)
        <div class="summary">
            <h3>Resumo do Relatório</h3>
            <p><strong>Total de livros:</strong> {{ $books->count() }}</p>
            <p><strong>Valor total do acervo:</strong> R$ {{ number_format($books->sum('Valor'), 2, ',', '.') }}</p>
            <p><strong>Valor médio por livro:</strong> R$ {{ number_format($books->avg('Valor'), 2, ',', '.') }}</p>
            <p><strong>Ano mais antigo:</strong> {{ $books->min('AnoPublicacao') }}</p>
            <p><strong>Ano mais recente:</strong> {{ $books->max('AnoPublicacao') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="8%">Código</th>
                    <th width="25%">Título</th>
                    <th width="15%">Editora</th>
                    <th width="8%">Edição</th>
                    <th width="8%">Ano</th>
                    <th width="20%">Autores</th>
                    <th width="16%">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr>
                    <td class="text-center">{{ $book->CodL }}</td>
                    <td>{{ $book->Titulo }}</td>
                    <td>{{ $book->Editora }}</td>
                    <td class="text-center">{{ $book->Edicao }}</td>
                    <td class="text-center">{{ $book->AnoPublicacao }}</td>
                    <td class="authors">
                        @if($book->authors->count() > 0)
                            {{ $book->authors->pluck('Nome')->join(', ') }}
                        @else
                            <em>Não informado</em>
                        @endif
                    </td>
                    <td class="text-right value">R$ {{ number_format($book->Valor, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="6" class="text-right">Total Geral:</td>
                    <td class="text-right value">R$ {{ number_format($books->sum('Valor'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        @if($books->count() > 20)
        <div style="page-break-before: always;">
            <h3>Estatísticas Adicionais</h3>
            
            <h4>Distribuição por Década</h4>
            <table style="width: 50%;">
                <thead>
                    <tr>
                        <th>Década</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-right">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $decadas = $books->groupBy(function($book) {
                            return floor($book->AnoPublicacao / 10) * 10;
                        })->sortKeys();
                    @endphp
                    @foreach($decadas as $decada => $booksDecada)
                    <tr>
                        <td>{{ $decada }}s</td>
                        <td class="text-center">{{ $booksDecada->count() }}</td>
                        <td class="text-right value">R$ {{ number_format($booksDecada->sum('Valor'), 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    @else
        <div class="no-books">
            <h3>Nenhum livro encontrado</h3>
            <p>Não foram encontrados livros que atendam aos critérios especificados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo Sistema de Gerenciamento de Biblioteca</p>
        <p>Página 1 de 1 | {{ $books->count() }} registro(s) encontrado(s)</p>
    </div>
</body>
</html>