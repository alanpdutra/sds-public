@extends('layouts.app')

@section('title', 'Novo Livro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-plus"></i> Novo Livro</h4>
  <a href="{{ url('/books') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card">
  <div class="card-body">
    <form id="bookForm" novalidate>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Título *</label>
          <input name="Titulo" id="Titulo" class="form-control" maxlength="40" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Editora *</label>
          <input name="Editora" id="Editora" class="form-control" maxlength="40" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Edição *</label>
          <input name="Edicao" id="Edicao" class="form-control" inputmode="numeric" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Ano *</label>
          <input name="AnoPublicacao" id="AnoPublicacao" class="form-control" inputmode="numeric" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Valor</label>
          <input name="Valor" id="Valor" class="form-control" placeholder="0,00">
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-12">
          <label class="form-label">Autores</label>
          <select id="authors" class="form-select" multiple size="5"></select>
          <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-12">
          <label class="form-label">Assuntos</label>
          <select id="subjects" class="form-select" multiple size="5"></select>
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button id="submitBtn" type="submit" class="btn btn-primary">
          <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpinner"></span>
          Salvar
        </button>
        <a href="{{ url('/books') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/api-client.js') }}"></script>
<script src="{{ asset('js/form-utils.js') }}"></script>
@vite(['resources/js/masks.js'])
<script>
  document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('bookForm');
    const btn = document.getElementById('submitBtn');
    const authorsSelect = document.getElementById('authors');
    const subjectsSelect = document.getElementById('subjects');

    window.mask.applyMoneyMask(document.getElementById('Valor'));
    window.mask.applyYearMask(document.getElementById('AnoPublicacao'));

    const [authorsRes, subjectsRes] = await Promise.all([
      window.api.listAuthors(), window.api.listSubjects()
    ]);
    authorsSelect.innerHTML = (authorsRes.ok ? (authorsRes.data || []) : []).map(a => `<option value="${a.CodAu}">${a.Nome}</option>`).join('');
    subjectsSelect.innerHTML = (subjectsRes.ok ? (subjectsRes.data || []) : []).map(s => `<option value="${s.CodAs}">${s.Descricao}</option>`).join('');

    window.formUtils.attachRealtimeValidation(form, btn);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const ok = window.formUtils.validateClientSide(form, window.mask.removeMoney);
      if (!ok) return;

      window.formUtils.buttonLoadingOn(btn, 'Salvando...');
      window.formUtils.showOverlay();

      const payload = {
        Titulo: form.Titulo.value.trim(),
        Editora: form.Editora.value.trim(),
        Edicao: Number(form.Edicao.value || 0),
        AnoPublicacao: Number(form.AnoPublicacao.value || 0),
        Valor: window.mask.removeMoney(form.Valor.value || '0'),
        authors: Array.from(authorsSelect.selectedOptions).map(o => Number(o.value)),
        subjects: Array.from(subjectsSelect.selectedOptions).map(o => Number(o.value)),
      };

      const res = await window.api.createBook(payload);
      window.formUtils.buttonLoadingOff(btn);
      window.formUtils.hideOverlay();
      if (res && res.ok) {
        try { localStorage.setItem('UIFxPendingToast', JSON.stringify({ text: 'Registro criado com sucesso.', variant: 'success' })); } catch (_) {}
        window.location.href = '/books';
      } else if (res && res.type === 'VALIDATION_ERROR') {
        // Mapear campos
        Object.entries(res.fields || {}).forEach(([name, messages]) => {
          const field = form.querySelector(`[name="${name}"]`) || form.querySelector(`#${name}`);
          if (field) window.formUtils.setFieldError(field, Array.isArray(messages) ? messages[0] : String(messages));
        });
        window.formUtils.showToast({ title: 'Erro de validação', text: 'Revise os campos destacados.', variant: 'error' });
      } else {
        window.formUtils.showToast({ title: 'Erro', text: (res && res.message) ? res.message : 'Falha ao processar a operação.', variant: 'error' });
      }
    });
  });
</script>
@endsection
