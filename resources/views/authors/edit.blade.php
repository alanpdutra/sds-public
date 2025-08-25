@extends('layouts.app')

@section('title', 'Editar Autor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Autor</h4>
  <a href="{{ url('/authors') }}" class="btn btn-outline-secondary">Voltar</a>
  <input type="hidden" id="authorId" value="{{ $id }}">
</div>

<div class="card">
  <div class="card-body">
    <form id="authorForm" novalidate>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome *</label>
          <input name="Nome" id="Nome" class="form-control" maxlength="40" required>
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button id="submitBtn" type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ url('/authors') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/api-client.js') }}"></script>
<script src="{{ asset('js/form-utils.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', async () => {
    const id = Number(document.getElementById('authorId').value);
    const form = document.getElementById('authorForm');
    const btn = document.getElementById('submitBtn');

    const res = await window.api.getAuthor(id);
    if (res && res.ok) {
      form.Nome.value = res.data.Nome || '';
    }

    window.formUtils.attachRealtimeValidation(form, btn);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      window.formUtils.syncBootstrapValidity(form);
      if (!form.Nome.value.trim() || form.Nome.value.trim().length > 40) {
        window.formUtils.setFieldError(form.Nome, 'Nome é obrigatório (até 40).');
        return;
      }
      window.formUtils.buttonLoadingOn(btn, 'Atualizando...');
      window.formUtils.showOverlay();
      const resUpd = await window.api.updateAuthor(id, { Nome: form.Nome.value.trim() });
      window.formUtils.buttonLoadingOff(btn);
      window.formUtils.hideOverlay();
      if (resUpd && resUpd.ok) {
        try { localStorage.setItem('UIFxPendingToast', JSON.stringify({ text: 'Registro atualizado com sucesso.', variant: 'success' })); } catch (_) {}
        window.location.href = '/authors';
      } else if (resUpd && resUpd.type === 'VALIDATION_ERROR') {
        Object.entries(resUpd.fields || {}).forEach(([name, messages]) => {
          const field = form.querySelector(`[name="${name}"]`);
          if (field) window.formUtils.setFieldError(field, Array.isArray(messages) ? messages[0] : String(messages));
        });
        window.formUtils.showToast({ title: 'Erro de validação', text: 'Revise os campos destacados.', variant: 'error' });
      } else {
        window.formUtils.showToast({ title: 'Erro', text: 'Falha ao processar a operação.', variant: 'error' });
      }
    });
  });
</script>
@endsection
