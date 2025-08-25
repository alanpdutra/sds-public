(() => {
  function showConfirmModal(title, message, onConfirm, onCancel) {
    const modalId = 'confirmModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
      modal = document.createElement('div');
      modal.id = modalId;
      modal.className = 'modal fade';
      modal.setAttribute('tabindex', '-1');
      modal.setAttribute('aria-labelledby', 'confirmModalLabel');
      modal.setAttribute('aria-hidden', 'true');
      
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmModalLabel">Confirmação</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p id="confirmModalMessage">Tem certeza?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="confirmModalCancel">Cancelar</button>
              <button type="button" class="btn btn-danger" id="confirmModalConfirm">Confirmar</button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
    }
    
    const titleEl = modal.querySelector('#confirmModalLabel');
    const messageEl = modal.querySelector('#confirmModalMessage');
    const confirmBtn = modal.querySelector('#confirmModalConfirm');
    const cancelBtn = modal.querySelector('#confirmModalCancel');
    
    titleEl.textContent = title || 'Confirmação';
    messageEl.textContent = message || 'Tem certeza?';
    
    // Remove previous event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    newConfirmBtn.addEventListener('click', () => {
      if (typeof onConfirm === 'function') onConfirm();
      bootstrap.Modal.getInstance(modal)?.hide();
    });
    
    newCancelBtn.addEventListener('click', () => {
      if (typeof onCancel === 'function') onCancel();
      bootstrap.Modal.getInstance(modal)?.hide();
    });
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    return bsModal;
  }
  
  function showInfoModal(title, message, onClose) {
    const modalId = 'infoModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
      modal = document.createElement('div');
      modal.id = modalId;
      modal.className = 'modal fade';
      modal.setAttribute('tabindex', '-1');
      modal.setAttribute('aria-labelledby', 'infoModalLabel');
      modal.setAttribute('aria-hidden', 'true');
      
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="infoModalLabel">Informação</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p id="infoModalMessage">Informação</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="infoModalClose">OK</button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
    }
    
    const titleEl = modal.querySelector('#infoModalLabel');
    const messageEl = modal.querySelector('#infoModalMessage');
    const closeBtn = modal.querySelector('#infoModalClose');
    
    titleEl.textContent = title || 'Informação';
    messageEl.textContent = message || 'Informação';
    
    // Remove previous event listeners
    const newCloseBtn = closeBtn.cloneNode(true);
    closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);
    
    newCloseBtn.addEventListener('click', () => {
      if (typeof onClose === 'function') onClose();
      bootstrap.Modal.getInstance(modal)?.hide();
    });
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    return bsModal;
  }
  
  function navigateTo(url, replace = false) {
    if (replace) {
      window.location.replace(url);
    } else {
      window.location.href = url;
    }
  }
  
  function reloadPage() {
    window.location.reload();
  }
  
  function goBack() {
    if (window.history.length > 1) {
      window.history.back();
    } else {
      navigateTo('/');
    }
  }
  
  function setPageTitle(title) {
    document.title = title ? `${title} - Spassu Saber` : 'Spassu Saber';
  }
  
  function updateBreadcrumb(items) {
    const breadcrumb = document.querySelector('.breadcrumb');
    if (!breadcrumb) return;
    
    breadcrumb.innerHTML = '';
    
    items.forEach((item, index) => {
      const li = document.createElement('li');
      li.className = 'breadcrumb-item';
      
      if (index === items.length - 1) {
        li.className += ' active';
        li.setAttribute('aria-current', 'page');
        li.textContent = item.text;
      } else {
        const a = document.createElement('a');
        a.href = item.url || '#';
        a.textContent = item.text;
        li.appendChild(a);
      }
      
      breadcrumb.appendChild(li);
    });
  }
  
  function highlightNavItem(path) {
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === path) {
        link.classList.add('active');
      }
    });
  }
  
  function formatDateTime(dateString, options = {}) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    const defaultOptions = {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'America/Sao_Paulo'
    };
    
    return date.toLocaleString('pt-BR', { ...defaultOptions, ...options });
  }
  
  function formatDate(dateString) {
    return formatDateTime(dateString, { hour: undefined, minute: undefined });
  }
  
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
  
  function throttle(func, limit) {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
  
  function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    } else {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.left = '-999999px';
      textArea.style.top = '-999999px';
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      return new Promise((resolve, reject) => {
        document.execCommand('copy') ? resolve() : reject();
        textArea.remove();
      });
    }
  }
  
  function scrollToTop(smooth = true) {
    window.scrollTo({
      top: 0,
      behavior: smooth ? 'smooth' : 'auto'
    });
  }
  
  function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }
  
  // Export to global scope
  window.uiUtils = {
    showConfirmModal,
    showInfoModal,
    navigateTo,
    reloadPage,
    goBack,
    setPageTitle,
    updateBreadcrumb,
    highlightNavItem,
    formatDateTime,
    formatDate,
    debounce,
    throttle,
    copyToClipboard,
    scrollToTop,
    isElementInViewport
  };
})();