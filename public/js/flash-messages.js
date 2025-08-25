/**
 * Flash Messages Handler
 * Processa mensagens flash do Laravel e exibe como toasts
 */

(function() {
    'use strict';

    // Aguarda o DOM estar pronto
    document.addEventListener('DOMContentLoaded', function() {
        processFlashMessages();
        processPendingToasts();
    });

    /**
     * Processa mensagens flash do Laravel
     */
    function processFlashMessages() {
        const flashDataElement = document.getElementById('flash-messages-data');
        if (!flashDataElement) return;

        try {
            const flashData = JSON.parse(flashDataElement.textContent || '{}');
            
            // Processa cada tipo de mensagem
            Object.entries(flashData).forEach(([type, message]) => {
                if (message && window.toastManager) {
                    showFlashMessage(type, message);
                }
            });

            // Remove o elemento após processar
            flashDataElement.remove();
        } catch (error) {
            console.error('Erro ao processar mensagens flash:', error);
        }
    }

    /**
     * Exibe uma mensagem flash como toast
     */
    function showFlashMessage(type, message) {
        if (!window.toastManager) {
            console.warn('ToastManager não disponível');
            return;
        }

        switch (type.toLowerCase()) {
            case 'success':
            case 'sucesso':
                window.toastManager.success(message);
                break;
            case 'error':
            case 'erro':
            case 'danger':
                window.toastManager.error(message);
                break;
            case 'warning':
            case 'aviso':
                window.toastManager.warning(message);
                break;
            case 'info':
            case 'informacao':
            case 'informação':
                window.toastManager.info(message);
                break;
            default:
                window.toastManager.info(message);
        }
    }

    /**
     * Processa toasts pendentes do localStorage
     */
    function processPendingToasts() {
        // Processa toasts do novo formato
        try {
            const pendingToasts = localStorage.getItem('pendingToasts');
            if (pendingToasts) {
                const toasts = JSON.parse(pendingToasts);
                if (Array.isArray(toasts)) {
                    toasts.forEach(toast => {
                        if (toast.message && window.toastManager) {
                            showFlashMessage(toast.type || 'info', toast.message);
                        }
                    });
                }
                localStorage.removeItem('pendingToasts');
            }
        } catch (error) {
            console.error('Erro ao processar toasts pendentes:', error);
            localStorage.removeItem('pendingToasts');
        }

        // Processa toasts do formato UIFxPendingToast (compatibilidade)
        try {
            const uiFxToast = localStorage.getItem('UIFxPendingToast');
            if (uiFxToast) {
                const toast = JSON.parse(uiFxToast);
                if (toast.text && window.toastManager) {
                    showFlashMessage(toast.variant || 'info', toast.text);
                }
                localStorage.removeItem('UIFxPendingToast');
            }
        } catch (error) {
            console.error('Erro ao processar UIFxPendingToast:', error);
            localStorage.removeItem('UIFxPendingToast');
        }
    }

    /**
     * Adiciona um toast pendente ao localStorage
     * Útil para exibir toasts após redirecionamentos
     */
    function addPendingToast(type, message) {
        try {
            const pendingToasts = JSON.parse(localStorage.getItem('pendingToasts') || '[]');
            pendingToasts.push({ type, message, timestamp: Date.now() });
            
            // Limita a 10 toasts pendentes
            if (pendingToasts.length > 10) {
                pendingToasts.splice(0, pendingToasts.length - 10);
            }
            
            localStorage.setItem('pendingToasts', JSON.stringify(pendingToasts));
        } catch (error) {
            console.error('Erro ao adicionar toast pendente:', error);
        }
    }

    /**
     * Limpa toasts pendentes antigos (mais de 1 hora)
     */
    function cleanupOldPendingToasts() {
        try {
            const pendingToasts = JSON.parse(localStorage.getItem('pendingToasts') || '[]');
            const oneHourAgo = Date.now() - (60 * 60 * 1000);
            
            const validToasts = pendingToasts.filter(toast => 
                toast.timestamp && toast.timestamp > oneHourAgo
            );
            
            if (validToasts.length !== pendingToasts.length) {
                localStorage.setItem('pendingToasts', JSON.stringify(validToasts));
            }
        } catch (error) {
            console.error('Erro ao limpar toasts antigos:', error);
            localStorage.removeItem('pendingToasts');
        }
    }

    // Exporta funções para uso global
    if (typeof window !== 'undefined') {
        window.flashMessages = {
            addPendingToast,
            processPendingToasts,
            cleanupOldPendingToasts
        };
    }

    // Limpa toasts antigos ao carregar
    cleanupOldPendingToasts();
})();