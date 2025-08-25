@extends('layouts.app')

@section('title', 'Assuntos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-tags"></i> Assuntos</h4>
  <a href="{{ url('/subjects/create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Novo</a>
  </div>

<div class="card">
  <div class="card-body">
    <div class="row g-2 mb-3">
      <div class="col-md-6">
        <input id="filterDescricao" class="form-control" placeholder="Filtrar por descrição">
      </div>
      <div class="col-md-2 d-grid">
        <button id="btnFiltrar" class="btn btn-outline-secondary">Filtrar</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center" id="paginationWrap">
      <div class="text-muted" id="paginationInfo"></div>
      <div class="btn-group" id="paginationBtns"></div>
    </div>
  </div>
</div>

<script src="{{ asset('js/api-client.js') }}"></script>
<script src="{{ asset('js/form-utils.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('tbody');
    const info = document.getElementById('paginationInfo');
    const btns = document.getElementById('paginationBtns');
    const descricao = document.getElementById('filterDescricao');
    const btnFiltrar = document.getElementById('btnFiltrar');

    let page = 1;

    const skeleton = () => {
      const row = `<tr><td colspan=\"3\"><div class=\"placeholder-glow\"><span class=\"placeholder col-12\"></span></div></td></tr>`;
      tbody.innerHTML = row.repeat(5);
    };

    const load = async () => {
      skeleton();
      window.formUtils.showOverlay();
      try {
        const res = await window.api.listSubjectsCrud({ page, descricao: descricao.value });
        const data = res && res.ok ? (res.data || []) : [];
        tbody.innerHTML = '';
        data.forEach(s => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td><strong>#${s.CodAs}</strong></td>
            <td>${s.Descricao}</td>
            <td>
              <div class="btn-group">
                <a href="/subjects/${s.CodAs}/edit" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i> Editar</a>
                <button class="btn btn-sm btn-outline-danger js-delete" data-id="${s.CodAs}" data-name="${s.Descricao}"><i class="bi bi-trash"></i> Excluir</button>
              </div>
            </td>`;
          const delBtn = tr.querySelector('.js-delete');
          delBtn.addEventListener('click', async () => {
            const id = delBtn.dataset.id;
            const name = delBtn.dataset.name;
            const ok = await window.UIFx?.confirmDeletion({
              title: 'Confirmar exclusão',
              message: `Tem certeza que deseja excluir o assunto "${name}"?`,
            });
            if (!ok) return;
            try {
              window.formUtils.showOverlay();
              const resDel = await window.api.deleteSubject(id);
              window.formUtils.hideOverlay();
              if (resDel && resDel.ok) {
                window.formUtils.showToast({ title: 'Sucesso', text: 'Registro removido com sucesso.', variant: 'success' });
                load();
              } else {
                // Extract specific validation message if available
                let errorMessage = 'Falha ao processar a operação.';
                if (resDel?.type === 'VALIDATION_ERROR' && resDel?.fields) {
                  // Get first error message from any field
                  for (const field in resDel.fields) {
                    if (resDel.fields[field] && resDel.fields[field][0]) {
                      errorMessage = resDel.fields[field][0];
                      break;
                    }
                  }
                } else if (resDel?.message) {
                  errorMessage = resDel.message;
                }
                window.formUtils.showToast({ title: 'Erro', text: errorMessage, variant: 'error' });
              }
            } catch {
              window.formUtils.hideOverlay();
              window.formUtils.showToast({ title: 'Erro', text: 'Erro ao comunicar com a API.', variant: 'error' });
            }
          });
          tbody.appendChild(tr);
        });
        const p = (res && res.ok) ? (res.pagination || { page: 1, pages: 1, total: data.length }) : { page: 1, pages: 1, total: data.length };
        info.textContent = `Página ${p.page} de ${p.pages} • Total: ${p.total}`;
        btns.innerHTML = '';
        const prev = document.createElement('button');
        prev.className = 'btn btn-outline-secondary';
        prev.disabled = p.page <= 1;
        prev.textContent = 'Anterior';
        prev.onclick = () => { page = Math.max(1, p.page - 1); load(); };
        const next = document.createElement('button');
        next.className = 'btn btn-outline-secondary';
        next.disabled = p.page >= p.pages;
        next.textContent = 'Próxima';
        next.onclick = () => { page = Math.min(p.pages, p.page + 1); load(); };
        btns.appendChild(prev);
        btns.appendChild(next);
      } finally {
        window.formUtils.hideOverlay();
      }
    };

    btnFiltrar.addEventListener('click', () => { page = 1; load(); });
    load();
  });
</script>
@endsection
