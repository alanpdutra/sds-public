/**
 * Confirmation Modals - Sistema de modais de confirmação
 * Gerencia modais de confirmação para ações críticas
 */

(function() {
    'use strict';

    class ConfirmationModal {
        constructor() {
            this.modalElement = null;
            this.currentCallback = null;
            this.init();
        }

        init() {
            this.createModal();
            this.bindEvents();
        }

        createModal() {
            // Remove modal existente se houver
            const existing = document.getElementById('confirmation-modal');
            if (existing) {
                existing.remove();
            }

            // Cria novo modal
            this.modalElement = document.createElement('div');
            this.modalElement.id = 'confirmation-modal';
            this.modalElement.className = 'modal fade';
            this.modalElement.setAttribute('tabindex', '-1');
            this.modalElement.setAttribute('aria-labelledby', 'confirmationModalLabel');
            this.modalElement.setAttribute('aria-hidden', 'true');
            
            this.modalElement.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirmar Ação</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center">
                                <div class="text-warning me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <p class="mb-0" id="confirmation-message">Tem certeza que deseja realizar esta ação?</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="confirm-action-btn">Confirmar</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(this.modalElement);
        }

        bindEvents() {
            // Evento de confirmação
            const confirmBtn = this.modalElement.querySelector('#confirm-action-btn');
            confirmBtn.addEventListener('click', () => {
                this.handleConfirm();
            });

            // Evento de cancelamento
            this.modalElement.addEventListener('hidden.bs.modal', () => {
                this.handleCancel();
            });

            // Bind automático para elementos com data-confirm
            document.addEventListener('click', (e) => {
                const target = e.target.closest('[data-confirm]');
                if (target) {
                    e.preventDefault();
                    this.showFromElement(target);
                }
            });
        }

        show(options = {}) {
            const config = {
                title: 'Confirmar Ação',
                message: 'Tem certeza que deseja realizar esta ação?',
                confirmText: 'Confirmar',
                confirmClass: 'btn-danger',
                cancelText: 'Cancelar',
                onConfirm: null,
                onCancel: null,
                ...options
            };

            // Atualiza conteúdo do modal
            this.modalElement.querySelector('#confirmationModalLabel').textContent = config.title;
            this.modalElement.querySelector('#confirmation-message').textContent = config.message;
            
            const confirmBtn = this.modalElement.querySelector('#confirm-action-btn');
            confirmBtn.textContent = config.confirmText;
            confirmBtn.className = `btn ${config.confirmClass}`;
            
            const cancelBtn = this.modalElement.querySelector('[data-bs-dismiss="modal"]');
            cancelBtn.textContent = config.cancelText;

            // Armazena callbacks
            this.currentCallback = {
                onConfirm: config.onConfirm,
                onCancel: config.onCancel
            };

            // Exibe modal
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(this.modalElement);
                modal.show();
            }
        }

        showFromElement(element) {
            const config = {
                title: element.dataset.confirmTitle || 'Confirmar Ação',
                message: element.dataset.confirm || 'Tem certeza que deseja realizar esta ação?',
                confirmText: element.dataset.confirmText || 'Confirmar',
                confirmClass: element.dataset.confirmClass || 'btn-danger',
                cancelText: element.dataset.cancelText || 'Cancelar'
            };

            // Determina ação baseada no elemento
            let action = null;
            
            if (element.tagName === 'A' && element.href) {
                action = () => {
                    window.location.href = element.href;
                };
            } else if (element.tagName === 'BUTTON' && element.form) {
                action = () => {
                    element.form.submit();
                };
            } else if (element.dataset.action) {
                action = () => {
                    this.executeAction(element.dataset.action, element);
                };
            } else if (element.onclick) {
                action = () => {
                    element.onclick.call(element);
                };
            }

            config.onConfirm = action;
            this.show(config);
        }

        executeAction(actionName, element) {
            switch (actionName) {
                case 'delete':
                    this.handleDelete(element);
                    break;
                case 'submit':
                    if (element.form) {
                        element.form.submit();
                    }
                    break;
                case 'redirect':
                    const url = element.dataset.url || element.href;
                    if (url) {
                        window.location.href = url;
                    }
                    break;
                default:
                    console.warn(`Ação não reconhecida: ${actionName}`);
            }
        }

        async handleDelete(element) {
            const url = element.dataset.url || element.href;
            const method = element.dataset.method || 'DELETE';
            
            if (!url) {
                console.error('URL não especificada para ação de delete');
                return;
            }

            try {
                if (window.loadingManager) {
                    window.loadingManager.showPageLoading();
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                const result = await response.json();

                if (result.success) {
                    if (window.toastManager) {
                        window.toastManager.success(result.message || 'Registro excluído com sucesso.');
                    }
                    
                    // Recarrega página ou remove elemento
                    setTimeout(() => {
                        if (element.dataset.reload !== 'false') {
                            window.location.reload();
                        } else {
                            this.removeElementFromDOM(element);
                        }
                    }, 1000);
                } else {
                    if (window.toastManager) {
                        window.toastManager.error(result.message || 'Erro ao excluir registro.');
                    }
                }
            } catch (error) {
                console.error('Erro na requisição de delete:', error);
                if (window.toastManager) {
                    window.toastManager.error('Erro ao comunicar com o servidor.');
                }
            } finally {
                if (window.loadingManager) {
                    window.loadingManager.hidePageLoading();
                }
            }
        }

        removeElementFromDOM(element) {
            // Tenta encontrar a linha da tabela ou card container
            const row = element.closest('tr, .card, .list-group-item, [data-item]');
            if (row) {
                row.style.transition = 'opacity 0.3s ease';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                }, 300);
            }
        }

        getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        }

        handleConfirm() {
            if (this.currentCallback && this.currentCallback.onConfirm) {
                this.currentCallback.onConfirm();
            }
            this.hideModal();
        }

        handleCancel() {
            if (this.currentCallback && this.currentCallback.onCancel) {
                this.currentCallback.onCancel();
            }
            this.currentCallback = null;
        }

        hideModal() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(this.modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        }

        // Métodos estáticos para uso direto
        static confirm(message, onConfirm, options = {}) {
            if (!window.confirmationModal) {
                window.confirmationModal = new ConfirmationModal();
            }
            
            window.confirmationModal.show({
                message,
                onConfirm,
                ...options
            });
        }

        static delete(url, options = {}) {
            const config = {
                title: 'Confirmar Exclusão',
                message: 'Tem certeza que deseja excluir este registro? Esta ação não pode ser desfeita.',
                confirmText: 'Excluir',
                confirmClass: 'btn-danger',
                ...options
            };

            this.confirm(config.message, () => {
                // Implementar lógica de delete
                console.log('Delete:', url);
            }, config);
        }
    }

    // Auto-inicialização
    document.addEventListener('DOMContentLoaded', function() {
        window.confirmationModal = new ConfirmationModal();
    });

    // Função confirmDeletion para compatibilidade com código existente
    function confirmDeletion({ title, message }) {
        return new Promise(resolve => {
            const modalEl = document.getElementById('confirmDeletionModal');
            if (!modalEl) {
                console.error('Modal confirmDeletionModal não encontrado');
                resolve(false);
                return;
            }
            
            modalEl.querySelector('.modal-title').textContent = title || 'Confirmar exclusão';
            modalEl.querySelector('#confirmDeletionMessage').textContent = message || 'Tem certeza que deseja excluir este registro?';
            const okBtn = modalEl.querySelector('#confirmDeletionOk');
            const spinner = okBtn.querySelector('.spinner-border');
            const cancelBtn = modalEl.querySelector('#confirmDeletionCancel');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            function cleanup(result) {
                okBtn.classList.remove('disabled');
                spinner.classList.add('d-none');
                okBtn.onclick = null;
                cancelBtn.onclick = null;
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
                resolve(result);
            }
            function onHidden() { cleanup(false); }
            modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });

            okBtn.onclick = () => {
                okBtn.classList.add('disabled');
                spinner.classList.remove('d-none');
                modal.hide();
                cleanup(true);
            };
            cancelBtn.onclick = () => { modal.hide(); };

            modal.show();
        });
    }

    // Exporta para uso global
    if (typeof window !== 'undefined') {
        window.ConfirmationModal = ConfirmationModal;
        window.UIFx = window.UIFx || {};
        window.UIFx.confirmDeletion = confirmDeletion;
    }
})();