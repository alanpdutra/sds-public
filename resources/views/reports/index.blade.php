@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-graph-up"></i> Relatório</h4>
  <div class="btn-group">
    <button id="btnCsv" class="btn btn-outline-primary"><i class="bi bi-download"></i> Exportar CSV</button>
    <button id="btnPdf" class="btn btn-outline-danger" data-url="{{ route('reports.pdf') }}">
      <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
    </button>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="p-3 border rounded text-center stats-card-books">
          <div class="text-muted">Total de Livros</div>
          <div id="booksTotal" class="fs-4 fw-semibold">-</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 border rounded text-center stats-card-authors">
          <div class="text-muted">Total de Autores</div>
          <div id="authorsTotal" class="fs-4 fw-semibold">-</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 border rounded text-center stats-card-subjects">
          <div class="text-muted">Total de Assuntos</div>
          <div id="subjectsTotal" class="fs-4 fw-semibold">-</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 border rounded text-center stats-card-updated">
          <div class="text-muted">Atualizado em</div>
          <div id="updatedAt" class="fs-6">-</div>
        </div>
      </div>
    </div>

    <h6 class="mt-3">Últimos Livros</h6>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Título</th>
            <th>Ano</th>
          </tr>
        </thead>
        <tbody id="latestBooks"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', async () => {
    const booksTotal = document.getElementById('booksTotal');
    const authorsTotal = document.getElementById('authorsTotal');
    const subjectsTotal = document.getElementById('subjectsTotal');
    const updatedAt = document.getElementById('updatedAt');
    const latestBooks = document.getElementById('latestBooks');
    const btnCsv = document.getElementById('btnCsv');

    window.formUtils.showOverlay();
    const res = await window.api.getSummary();
    window.formUtils.hideOverlay();
    if (!res || !res.ok) {
      window.formUtils?.showToast({ title: 'Erro', text: 'Erro ao carregar relatório', variant: 'erro' });
      return;
    }
    const d = res.data;
    booksTotal.textContent = d.booksTotal;
    authorsTotal.textContent = d.authorsTotal;
    subjectsTotal.textContent = d.subjectsTotal;
    updatedAt.textContent = new Date(d.updatedAt).toLocaleString('pt-BR');
    latestBooks.innerHTML = (d.latestBooks || []).map(b => `
      <tr><td>#${b.CodL}</td><td>${b.Titulo}</td><td>${b.AnoPublicacao}</td></tr>
    `).join('');

    btnCsv.addEventListener('click', () => {
      const rows = [
        ['booksTotal', d.booksTotal],
        ['authorsTotal', d.authorsTotal],
        ['subjectsTotal', d.subjectsTotal],
      ];
      rows.push([]);
      rows.push(['CodL', 'Titulo', 'AnoPublicacao']);
      (d.latestBooks || []).forEach(b => rows.push([b.CodL, b.Titulo, b.AnoPublicacao]));

      const csv = rows.map(r => r.map(String).map(v => '"' + v.replaceAll('"', '""') + '"').join(',')).join('\n');
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'relatorio.csv';
      a.click();
      URL.revokeObjectURL(url);
    });

    // PDF export with loader
    const btnPdf = document.getElementById('btnPdf');
    btnPdf.addEventListener('click', () => {
      const originalContent = btnPdf.innerHTML;
      btnPdf.disabled = true;
      btnPdf.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Gerando PDF...';
      
      // Create a temporary link to download the PDF
      const link = document.createElement('a');
      link.href = btnPdf.dataset.url;
      link.target = '_blank';
      
      // Reset button after a delay (simulating PDF generation time)
      setTimeout(() => {
        btnPdf.disabled = false;
        btnPdf.innerHTML = originalContent;
        link.click();
      }, 1500);
    });
  });
</script>
@endsection
