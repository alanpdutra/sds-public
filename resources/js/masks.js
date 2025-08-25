export {};

function applyMoneyMask(element) {
  if (!element) return;
  const format = (raw) => {
    let v = String(raw || '').replace(/\D/g, '');
    if (!v) return '';
    
    // Se o valor tem menos de 3 dígitos, trata como centavos
    if (v.length <= 2) {
      v = v.padStart(2, '0');
      return `0,${v}`;
    }
    
    // Se tem 3 ou mais dígitos, os últimos 2 são centavos
    const reais = v.slice(0, -2).replace(/^0+/, '') || '0';
    const cent = v.slice(-2);
    const reaisFmt = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return `${reaisFmt},${cent}`;
  };
  element.addEventListener('input', (e) => {
    e.target.value = format(e.target.value);
  });
  element.addEventListener('blur', (e) => {
    e.target.value = format(e.target.value);
  });
}

function applyYearMask(element) {
  if (!element) return;
  element.addEventListener('input', (e) => {
    e.target.value = String(e.target.value || '').replace(/\D/g, '').slice(0, 4);
  });
  element.addEventListener('blur', (e) => {
    const y = Number(String(e.target.value || '').slice(0, 4));
    const cur = new Date().getFullYear();
    const invalid = !y || y < 1500 || y > cur;
    const group = e.target.closest('.col, .mb-3, .form-group') || e.target.parentElement || e.target;
    let fb = group.querySelector('.invalid-feedback');
    if (!fb) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      group.appendChild(fb);
    }
    if (invalid) {
      e.target.classList.add('is-invalid');
      fb.textContent = `O ano deve estar entre 1500 e ${cur}.`;
    } else {
      e.target.classList.remove('is-invalid');
      fb.textContent = '';
    }
  });
}

function removeMoney(value) {
  if (value === null || value === undefined) return 0;
  let s = String(value).replace(/[^\d,.-]/g, '');
  // Remove pontos que são separadores de milhares (antes da vírgula)
  s = s.replace(/\.(?=\d{3}(,|$))/g, '');
  // Substitui vírgula por ponto decimal
  s = s.replace(',', '.');
  const n = parseFloat(s);
  return Number.isFinite(n) ? n : 0;
}

function formatMoney(value) {
  const n = Number(value || 0);
  if (!Number.isFinite(n)) return 'R$ 0,00';
  return 'R$ ' + n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

window.mask = { applyMoneyMask, applyYearMask, removeMoney, formatMoney };
