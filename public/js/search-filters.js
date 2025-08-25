/**
 * Search Filters - Sistema de filtros de busca
 * Gerencia filtros, paginação e busca em tempo real
 */

(function() {
    'use strict';

    class SearchFilters {
        constructor(options = {}) {
            this.options = {
                searchInputSelector: '#search',
                filterFormSelector: '#filter-form',
                resultsContainerSelector: '#results-container',
                paginationSelector: '.pagination',
                loadingSelector: '#loading',
                debounceDelay: 300,
                ...options
            };

            this.searchTimeout = null;
            this.currentPage = 1;
            this.currentFilters = {};
            
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadInitialFilters();
        }

        bindEvents() {
            // Busca em tempo real
            const searchInput = document.querySelector(this.options.searchInputSelector);
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.handleSearch(e.target.value);
                });
            }

            // Filtros do formulário
            const filterForm = document.querySelector(this.options.filterFormSelector);
            if (filterForm) {
                filterForm.addEventListener('change', (e) => {
                    this.handleFilterChange(e);
                });

                filterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.applyFilters();
                });
            }

            // Botão de limpar filtros
            const clearButton = document.querySelector('[data-action="clear-filters"]');
            if (clearButton) {
                clearButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.clearFilters();
                });
            }

            // Paginação
            this.bindPaginationEvents();
        }

        bindPaginationEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.pagination a[data-page]')) {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page);
                    if (page && page !== this.currentPage) {
                        this.goToPage(page);
                    }
                }
            });
        }

        handleSearch(query) {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentFilters.search = query;handleSearch
                this.currentPage = 1;
                this.applyFilters();
            }, this.options.debounceDelay);
        }

        handleFilterChange(event) {
            const target = event.target;
            const name = target.name;
            const value = target.type === 'checkbox' ? target.checked : target.value;
            
            this.currentFilters[name] = value;
            
            // Auto-aplicar filtros para alguns tipos
            if (target.type === 'checkbox' || target.tagName === 'SELECT') {
                this.currentPage = 1;
                this.applyFilters();
            }
        }

        applyFilters() {
            const params = new URLSearchParams();
            
            // Adiciona filtros atuais
            Object.entries(this.currentFilters).forEach(([key, value]) => {
                if (value !== '' && value !== null && value !== undefined) {
                    params.append(key, value);
                }
            });
            
            // Adiciona página atual
            if (this.currentPage > 1) {
                params.append('page', this.currentPage);
            }
            
            this.loadResults(params);
        }

        async loadResults(params) {
            try {
                this.showLoading(true);
                
                const url = new URL(window.location.href);
                url.search = params.toString();
                
                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const html = await response.text();
                this.updateResults(html);
                
                // Atualiza URL sem recarregar a página
                window.history.replaceState({}, '', url.toString());
                
            } catch (error) {
                console.error('Erro ao carregar resultados:', error);
                if (window.toastManager) {
                    window.toastManager.error('Erro ao carregar resultados.');
                }
            } finally {
                this.showLoading(false);
            }
        }

        updateResults(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Atualiza container de resultados
            const newResults = doc.querySelector(this.options.resultsContainerSelector);
            const currentResults = document.querySelector(this.options.resultsContainerSelector);
            
            if (newResults && currentResults) {
                currentResults.innerHTML = newResults.innerHTML;
            }
            
            // Atualiza paginação
            const newPagination = doc.querySelector(this.options.paginationSelector);
            const currentPagination = document.querySelector(this.options.paginationSelector);
            
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
            
            // Re-bind eventos de paginação
            this.bindPaginationEvents();
        }

        goToPage(page) {
            this.currentPage = page;
            this.applyFilters();
        }

        clearFilters() {
            // Limpa formulário
            const filterForm = document.querySelector(this.options.filterFormSelector);
            if (filterForm) {
                filterForm.reset();
            }
            
            // Limpa busca
            const searchInput = document.querySelector(this.options.searchInputSelector);
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Reset filtros
            this.currentFilters = {};
            this.currentPage = 1;
            
            this.applyFilters();
        }

        loadInitialFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Carrega filtros da URL
            urlParams.forEach((value, key) => {
                if (key === 'page') {
                    this.currentPage = parseInt(value) || 1;
                } else {
                    this.currentFilters[key] = value;
                    
                    // Atualiza campos do formulário
                    const field = document.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = value === 'true' || value === '1';
                        } else {
                            field.value = value;
                        }
                    }
                }
            });
        }

        showLoading(show) {
            const loading = document.querySelector(this.options.loadingSelector);
            if (loading) {
                loading.style.display = show ? 'block' : 'none';
            }
            
            // Desabilita formulário durante carregamento
            const filterForm = document.querySelector(this.options.filterFormSelector);
            if (filterForm) {
                const inputs = filterForm.querySelectorAll('input, select, button');
                inputs.forEach(input => {
                    input.disabled = show;
                });
            }
        }

        // Métodos públicos para uso externo
        addFilter(name, value) {
            this.currentFilters[name] = value;
            this.currentPage = 1;
            this.applyFilters();
        }

        removeFilter(name) {
            delete this.currentFilters[name];
            this.currentPage = 1;
            this.applyFilters();
        }

        getFilters() {
            return { ...this.currentFilters };
        }

        refresh() {
            this.applyFilters();
        }
    }

    // Auto-inicialização
    document.addEventListener('DOMContentLoaded', function() {
        // Verifica se há elementos de filtro na página
        if (document.querySelector('#search') || document.querySelector('#filter-form')) {
            window.searchFilters = new SearchFilters();
        }
    });

    // Exporta para uso global
    if (typeof window !== 'undefined') {
        window.SearchFilters = SearchFilters;
    }
})();