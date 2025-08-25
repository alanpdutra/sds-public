<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Relatório de Livros por Autor</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
    h1 { font-size: 18px; margin: 0 0 8px; }
    h2 { font-size: 14px; margin: 16px 0 6px; }
    .meta { font-size: 11px; color: #555; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; }
    th { background: #f3f4f6; text-align: left; }
    .text-end { text-align: right; }
    .total { font-weight: bold; }
    .grand-total { margin-top: 12px; font-size: 13px; font-weight: bold; }
  </style>
  </head>
  <body>
    <h1>Relatório de Livros por Autor</h1>
    <div class="meta">Gerado em: {{ $generatedAt }}</div>

    @forelse($authors as $autor => $data)
      <h2>{{ $autor }}</h2>
      <table>
        <thead>
          <tr>
            <th>Livro</th>
            <th class="text-end">Valor</th>
            <th>Assuntos</th>
          </tr>
        </thead>
        <tbody>
          @foreach($data['books'] as $b)
            <tr>
              <td>{{ $b['titulo'] }}</td>
              <td class="text-end">R$ {{ number_format($b['valor'] ?? 0, 2, ',', '.') }}</td>
              <td>{{ $b['assuntos'] }}</td>
            </tr>
          @endforeach
          <tr>
            <td colspan="3" class="total">Total do autor: R$ {{ number_format($data['total'] ?? 0, 2, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>
    @empty
      <p>Sem dados.</p>
    @endforelse

    <div class="grand-total">Total geral: R$ {{ number_format($grandTotal ?? 0, 2, ',', '.') }}</div>
  </body>
</html>

