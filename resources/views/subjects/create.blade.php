@extends('layouts.app')

@section('title', 'Novo Assunto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-plus"></i> Novo Assunto</h4>
  <a href="{{ url('/subjects') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card">
  <div class="card-body">
    <form id="subjectForm" novalidate>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Descrição *</label>
          <input name="Descricao" id="Descricao" class="form-control" maxlength="20" required>
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button id="submitBtn" type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ url('/subjects') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/api-client.js') }}"></script>
<script src="{{ asset('js/form-utils.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('subjectForm');
    const btn = document.getElementById('submitBtn');

    window.formUtils.attachRealtimeValidation(form, btn);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      window.formUtils.syncBootstrapValidity(form);
      if (!form.Descricao.value.trim() || form.Descricao.value.trim().length > 20) {
        window.formUtils.setFieldError(form.Descricao, 'Descrição é obrigatória (até 20).');
        return;
      }
      window.formUtils.buttonLoadingOn(btn, 'Salvando...');
      window.formUtils.showOverlay();
      const res = await window.api.createSubject({ Descricao: form.Descricao.value.trim() });
      window.formUtils.buttonLoadingOff(btn);
      window.formUtils.hideOverlay();
      if (res && res.ok) {
        try { localStorage.setItem('UIFxPendingToast', JSON.stringify({ text: 'Registro criado com sucesso.', variant: 'success' })); } catch (_) {}
        window.location.href = '/subjects';
      } else if (res && res.type === 'VALIDATION_ERROR') {
        Object.entries(res.fields || {}).forEach(([name, messages]) => {
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
