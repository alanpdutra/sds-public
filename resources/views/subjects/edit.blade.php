@extends('layouts.app')

@section('title', 'Editar Assunto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Assunto</h4>
  <a href="{{ url('/subjects') }}" class="btn btn-outline-secondary">Voltar</a>
  <input type="hidden" id="subjectId" value="{{ $id }}">
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
  document.addEventListener('DOMContentLoaded', async () => {
    const id = Number(document.getElementById('subjectId').value);
    const form = document.getElementById('subjectForm');
    const btn = document.getElementById('submitBtn');

    const res = await window.api.getSubject(id);
    if (res && res.ok) {
      form.Descricao.value = res.data.Descricao || '';
    }

    window.formUtils.attachRealtimeValidation(form, btn);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      window.formUtils.syncBootstrapValidity(form);
      if (!form.Descricao.value.trim() || form.Descricao.value.trim().length > 20) {
        window.formUtils.setFieldError(form.Descricao, 'Descrição é obrigatória (até 20).');
        return;
      }
      window.formUtils.buttonLoadingOn(btn, 'Atualizando...');
      window.formUtils.showOverlay();
      const resUpd = await window.api.updateSubject(id, { Descricao: form.Descricao.value.trim() });
      window.formUtils.buttonLoadingOff(btn);
      window.formUtils.hideOverlay();
      if (resUpd && resUpd.ok) {
        try { localStorage.setItem('UIFxPendingToast', JSON.stringify({ text: 'Registro atualizado com sucesso.', variant: 'success' })); } catch (_) {}
        window.location.href = '/subjects';
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
