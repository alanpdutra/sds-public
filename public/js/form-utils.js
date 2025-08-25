(() => {
  function getGroup(field) {
    return field.closest('.col, .mb-3, .form-group') || field.parentElement || field;
  }

  function ensureFeedbackEl(field) {
    const group = getGroup(field);
    let fb = group.querySelector('.invalid-feedback');
    if (!fb) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      group.appendChild(fb);
    }
    return fb;
  }

  function setFieldError(fieldEl, message) {
    if (!fieldEl) return;
    fieldEl.classList.add('is-invalid');
    const fb = ensureFeedbackEl(fieldEl);
    fb.textContent = message || 'Campo inválido';
  }

  function clearFieldError(fieldEl) {
    if (!fieldEl) return;
    fieldEl.classList.remove('is-invalid');
    const group = getGroup(fieldEl);
    const fb = group.querySelector('.invalid-feedback');
    if (fb) fb.textContent = '';
    if (typeof fieldEl.setCustomValidity === 'function') fieldEl.setCustomValidity('');
  }

  function syncBootstrapValidity(formEl) {
    if (!formEl) return;
    // Clear all
    Array.from(formEl.elements || []).forEach((el) => clearFieldError(el));
  }

  function buttonLoadingOn(btnEl, text = 'Salvando...') {
    if (!btnEl) return;
    if (!btnEl.dataset.originalText) {
      btnEl.dataset.originalText = btnEl.innerHTML;
    }
    btnEl.disabled = true;
    btnEl.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> ${text}`;
  }

  function buttonLoadingOff(btnEl) {
    if (!btnEl) return;
    btnEl.disabled = false;
    if (btnEl.dataset.originalText) {
      btnEl.innerHTML = btnEl.dataset.originalText;
    }
  }

  function getToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container position-fixed top-0 end-0 p-3';
      container.style.zIndex = '1080';
      document.body.appendChild(container);
    }
    return container;
  }

  function normalizeVariant(v) {
    const t = String(v || 'info').toLowerCase();
    if (t === 'sucesso' || t === 'success') return 'success';
    if (t === 'erro' || t === 'error' || t === 'danger') return 'danger';
    if (t === 'aviso' || t === 'warning') return 'warning';
    if (t === 'info' || t === 'informacao' || t === 'informação') return 'info';
    return 'info';
  }

  function showToast(optsOrTitle, text, variant) {
    const opts = typeof optsOrTitle === 'object' ? optsOrTitle : { title: optsOrTitle, text, variant };
    const title = opts.title || '';
    const body = opts.text || '';
    const type = normalizeVariant(opts.variant); // success|danger|warning|info

    const container = getToastContainer();
    const el = document.createElement('div');
    el.className = `toast align-items-center border-0 text-bg-${type}`;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');
    el.innerHTML = `
      <div class="toast-header text-bg-${type} border-0">
        <strong class="me-auto">${title}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">${body}</div>
    `;
    container.appendChild(el);
    const toast = bootstrap?.Toast ? new bootstrap.Toast(el, { autohide: true, delay: 4000 }) : null;
    if (toast) {
      toast.show();
      el.addEventListener('hidden.bs.toast', () => el.remove());
    }
    return el;
  }

  function ensureProgressBar() {
    let bar = document.getElementById('page-progress');
    if (!bar) {
      bar = document.createElement('div');
      bar.id = 'page-progress';
      bar.style.cssText = 'position:fixed;top:0;left:0;height:3px;width:0;background:#0d6efd;z-index:2001;transition:width .3s;';
      document.body.appendChild(bar);
    }
    return bar;
  }

  function showOverlay() {
    let ov = document.getElementById('globalOverlay');
    if (!ov) {
      ov = document.createElement('div');
      ov.id = 'globalOverlay';
      ov.style.position = 'fixed';
      ov.style.inset = '0';
      ov.style.background = 'rgba(255,255,255,0.8)';
      ov.style.backdropFilter = 'blur(2px)';
      ov.style.display = 'flex';
      ov.style.alignItems = 'center';
      ov.style.justifyContent = 'center';
      ov.style.zIndex = '2000';
      ov.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';
      document.body.appendChild(ov);
    }
    ov.style.display = 'flex';
    const bar = ensureProgressBar();
    bar.style.width = '60%';
  }

  function hideOverlay() {
    const ov = document.getElementById('globalOverlay');
    if (ov) ov.style.display = 'none';
    const bar = ensureProgressBar();
    bar.style.width = '100%';
    setTimeout(() => { bar.style.width = '0%'; }, 250);
  }

  function validateClientSide(formEl, removeMoneyFn) {
    const errors = {};
    const cur = new Date().getFullYear();
    const titulo = formEl.Titulo?.value?.trim() || '';
    const editora = formEl.Editora?.value?.trim() || '';
    const edicao = Number(formEl.Edicao?.value || '');
    const ano = Number(formEl.AnoPublicacao?.value || '');
    const valorRaw = formEl.Valor?.value ?? '';
    const valor = typeof removeMoneyFn === 'function' ? Number(removeMoneyFn(valorRaw)) : Number(valorRaw);

    if (!titulo || titulo.length > 40) {
      errors.Titulo = ['Título é obrigatório e deve ter até 40 caracteres.'];
    }
    if (!editora || editora.length > 40) {
      errors.Editora = ['Editora é obrigatória e deve ter até 40 caracteres.'];
    }
    if (!Number.isInteger(edicao) || edicao < 1) {
      errors.Edicao = ['Edição é obrigatória e deve ser maior que 0.'];
    }
    if (!Number.isInteger(ano) || ano < 1500 || ano > cur) {
      errors.AnoPublicacao = [`O ano deve estar entre 1500 e ${cur}.`];
    }
    if (valorRaw !== '' && (isNaN(valor) || valor < 0)) {
      errors.Valor = ['Valor deve ser maior ou igual a 0.'];
    }

    Array.from(formEl.elements).forEach((el) => clearFieldError(el));
    Object.entries(errors).forEach(([name, messages]) => {
      const field = formEl.querySelector(`[name="${name}"]`);
      if (field) {
        setFieldError(field, messages[0]);
        if (typeof field.setCustomValidity === 'function') field.setCustomValidity(messages[0]);
      }
    });

    const ok = Object.keys(errors).length === 0;
    
    if (ok && formEl.Valor) {
      const n = typeof removeMoneyFn === 'function' ? Number(removeMoneyFn(valorRaw)) : Number(valorRaw);
      if (!isNaN(n)) formEl.Valor.value = n.toFixed(2);
    }
    
    if (!ok) {
      const first = formEl.querySelector('.is-invalid') || formEl.querySelector('[name]:invalid');
      if (first) {
        first.focus({ preventScroll: true });
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
    return ok;
  }

  function attachRealtimeValidation(formEl, submitBtn) {
    const updateBtn = () => {
      if (submitBtn) submitBtn.disabled = !formEl.checkValidity();
    };
    formEl.addEventListener('input', (e) => {
      const t = e.target;
      if (t && t.name) {
        clearFieldError(t);
        if (typeof t.setCustomValidity === 'function') t.setCustomValidity('');
      }
      updateBtn();
    });
    formEl.addEventListener('change', updateBtn);
    updateBtn();
  }

  window.UIFx = {
    overlay: (on) => (on ? showOverlay() : hideOverlay()),
    toast: (msg, variant = 'info') => showToast({ title: variant === 'sucesso' ? 'Sucesso' : 'Info', text: msg, variant }),
  };

  window.formUtils = {
    setFieldError,
    clearFieldError,
    syncBootstrapValidity,
    buttonLoadingOn,
    buttonLoadingOff,
    showToast,
    showOverlay,
    hideOverlay,
    validateClientSide,
    attachRealtimeValidation,
  };
})();
