/**
 * Sistema de Feedback - ToastManager e LoadingManager
 * Gerencia toasts de notificação e estados de carregamento
 */

// ToastManager - Gerencia toasts de notificação
class ToastManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        this.container = this.getToastContainer();
    }

    getToastContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }
        return container;
    }

    normalizeVariant(variant) {
        const v = String(variant || 'info').toLowerCase();
        if (v === 'sucesso' || v === 'success') return 'success';
        if (v === 'erro' || v === 'error' || v === 'danger') return 'danger';
        if (v === 'aviso' || v === 'warning') return 'warning';
        if (v === 'info' || v === 'informacao' || v === 'informação') return 'info';
        return 'info';
    }

    show(message, variant = 'info', title = null) {
        const type = this.normalizeVariant(variant);
        const toastTitle = title || this.getDefaultTitle(type);
        
        const toastElement = document.createElement('div');
        toastElement.className = `toast align-items-center border-0 text-bg-${type}`;
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        
        toastElement.innerHTML = `
            <div class="toast-header text-bg-${type} border-0">
                <strong class="me-auto">${toastTitle}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;
        
        this.container.appendChild(toastElement);
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(toastElement, { 
                autohide: true, 
                delay: 4000 
            });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
        
        return toastElement;
    }

    getDefaultTitle(type) {
        switch (type) {
            case 'success': return 'Sucesso';
            case 'danger': return 'Erro';
            case 'warning': return 'Aviso';
            case 'info': return 'Informação';
            default: return 'Notificação';
        }
    }

    success(message, title = null) {
        return this.show(message, 'success', title);
    }

    error(message, title = null) {
        return this.show(message, 'error', title);
    }

    warning(message, title = null) {
        return this.show(message, 'warning', title);
    }

    info(message, title = null) {
        return this.show(message, 'info', title);
    }
}

// LoadingManager - Gerencia estados de carregamento
class LoadingManager {
    constructor() {
        this.loadingElements = new Map();
    }

    showButtonLoading(button, loadingText = 'Carregando...') {
        if (!button) return;
        
        // Salva o estado original
        const originalText = button.innerHTML;
        const originalDisabled = button.disabled;
        
        this.loadingElements.set(button, {
            originalText,
            originalDisabled
        });
        
        // Aplica o estado de carregamento
        button.disabled = true;
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ${loadingText}
        `;
    }

    hideButtonLoading(button) {
        if (!button || !this.loadingElements.has(button)) return;
        
        const originalState = this.loadingElements.get(button);
        
        // Restaura o estado original
        button.innerHTML = originalState.originalText;
        button.disabled = originalState.originalDisabled;
        
        // Remove do mapa
        this.loadingElements.delete(button);
    }

    showPageLoading() {
        let overlay = document.getElementById('page-loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'page-loading-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            `;
            overlay.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-2">Carregando...</div>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }

    hidePageLoading() {
        const overlay = document.getElementById('page-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    showProgressBar(progress = 0) {
        let bar = document.getElementById('page-progress-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'page-progress-bar';
            bar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                background: #0d6efd;
                z-index: 10000;
                transition: width 0.3s ease;
                width: 0%;
            `;
            document.body.appendChild(bar);
        }
        bar.style.width = `${Math.min(100, Math.max(0, progress))}%`;
        bar.style.display = 'block';
    }

    hideProgressBar() {
        const bar = document.getElementById('page-progress-bar');
        if (bar) {
            bar.style.display = 'none';
        }
    }
}

// NotificationManager - Gerencia notificações do sistema
class NotificationManager {
    constructor(toastManager) {
        this.toastManager = toastManager;
    }

    handleApiResponse(response, successMessage = null) {
        if (response.success) {
            if (successMessage) {
                this.toastManager.success(successMessage);
            }
        } else if (response.error) {
            const error = response.error;
            if (error.type === 'VALIDATION_ERROR') {
                this.toastManager.error('Revise os campos destacados.', 'Erro de validação');
            } else {
                this.toastManager.error(error.message || 'Falha ao processar a operação.');
            }
        }
    }

    handleNetworkError() {
        this.toastManager.error('Erro ao comunicar com a API.', 'Erro de rede');
    }

    showValidationErrors(errors) {
        if (typeof errors === 'object') {
            Object.entries(errors).forEach(([field, messages]) => {
                const fieldElement = document.querySelector(`[name="${field}"]`);
                if (fieldElement && window.formUtils) {
                    const message = Array.isArray(messages) ? messages[0] : String(messages);
                    window.formUtils.setFieldError(fieldElement, message);
                }
            });
        }
    }
}

// Exporta as classes para uso global
if (typeof window !== 'undefined') {
    window.ToastManager = ToastManager;
    window.LoadingManager = LoadingManager;
    window.NotificationManager = NotificationManager;
}