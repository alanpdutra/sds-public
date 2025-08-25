/**
 * DataTables Configuration - Configurações padrão para DataTables
 * Configurações globais e utilitários para tabelas interativas
 */

(function() {
    'use strict';

    // Configurações padrão em português
    const defaultLanguage = {
        "sEmptyTable": "Nenhum registro encontrado",
        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
        "sInfoThousands": ".",
        "sLengthMenu": "_MENU_ resultados por página",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sZeroRecords": "Nenhum registro encontrado",
        "sSearch": "Pesquisar",
        "oPaginate": {
            "sNext": "Próximo",
            "sPrevious": "Anterior",
            "sFirst": "Primeiro",
            "sLast": "Último"
        },
        "oAria": {
            "sSortAscending": ": Ordenar colunas de forma ascendente",
            "sSortDescending": ": Ordenar colunas de forma descendente"
        }
    };

    // Configurações padrão do DataTables
    const defaultConfig = {
        language: defaultLanguage,
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            {
                targets: 'no-sort',
                orderable: false
            },
            {
                targets: 'text-center',
                className: 'text-center'
            },
            {
                targets: 'text-right',
                className: 'text-end'
            }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Re-inicializa tooltips após redraw
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });
            }
        }
    };

    class DataTablesManager {
        constructor() {
            this.tables = new Map();
            this.init();
        }

        init() {
            // Aguarda o DOM estar pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.autoInitialize();
                });
            } else {
                this.autoInitialize();
            }
        }

        autoInitialize() {
            // Auto-inicializa tabelas com classe 'datatable'
            const tables = document.querySelectorAll('table.datatable');
            tables.forEach(table => {
                if (!table.id) {
                    table.id = 'datatable-' + Math.random().toString(36).substr(2, 9);
                }
                this.initializeTable(table.id);
            });
        }

        initializeTable(tableId, customConfig = {}) {
            const table = document.getElementById(tableId);
            if (!table) {
                console.warn(`Tabela com ID '${tableId}' não encontrada`);
                return null;
            }

            // Verifica se DataTables está disponível
            if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
                console.warn('DataTables não está disponível');
                return null;
            }

            try {
                // Mescla configurações
                const config = this.mergeConfigs(defaultConfig, customConfig, table);
                
                // Inicializa DataTable
                const dataTable = $(table).DataTable(config);
                
                // Armazena referência
                this.tables.set(tableId, dataTable);
                
                // Bind eventos customizados
                this.bindCustomEvents(tableId, dataTable);
                
                return dataTable;
            } catch (error) {
                console.error(`Erro ao inicializar DataTable '${tableId}':`, error);
                return null;
            }
        }

        mergeConfigs(defaultConfig, customConfig, table) {
            // Configurações baseadas em data attributes
            const dataConfig = this.getConfigFromDataAttributes(table);
            
            // Mescla todas as configurações
            return {
                ...defaultConfig,
                ...dataConfig,
                ...customConfig
            };
        }

        getConfigFromDataAttributes(table) {
            const config = {};
            
            // Page length
            if (table.dataset.pageLength) {
                config.pageLength = parseInt(table.dataset.pageLength);
            }
            
            // Ordering
            if (table.dataset.ordering === 'false') {
                config.ordering = false;
            }
            
            // Searching
            if (table.dataset.searching === 'false') {
                config.searching = false;
            }
            
            // Paging
            if (table.dataset.paging === 'false') {
                config.paging = false;
            }
            
            // Info
            if (table.dataset.info === 'false') {
                config.info = false;
            }
            
            // Server-side processing
            if (table.dataset.serverSide === 'true') {
                config.serverSide = true;
                config.ajax = {
                    url: table.dataset.ajaxUrl || window.location.href,
                    type: 'GET',
                    data: function(d) {
                        // Adiciona token CSRF se necessário
                        d._token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        return d;
                    }
                };
            }
            
            return config;
        }

        bindCustomEvents(tableId, dataTable) {
            const table = document.getElementById(tableId);
            
            // Evento de clique em linhas
            if (table.dataset.clickableRows === 'true') {
                $(table).on('click', 'tbody tr', function() {
                    const data = dataTable.row(this).data();
                    const url = this.dataset.href || table.dataset.rowUrl;
                    
                    if (url && data) {
                        // Substitui placeholders na URL
                        let finalUrl = url;
                        Object.keys(data).forEach(key => {
                            finalUrl = finalUrl.replace(`{${key}}`, data[key]);
                        });
                        
                        window.location.href = finalUrl;
                    }
                });
            }
            
            // Evento de refresh
            table.addEventListener('refresh', () => {
                this.refreshTable(tableId);
            });
        }

        refreshTable(tableId) {
            const dataTable = this.tables.get(tableId);
            if (dataTable) {
                if (dataTable.ajax) {
                    dataTable.ajax.reload(null, false); // Mantém paginação
                } else {
                    dataTable.draw(false);
                }
            }
        }

        destroyTable(tableId) {
            const dataTable = this.tables.get(tableId);
            if (dataTable) {
                dataTable.destroy();
                this.tables.delete(tableId);
            }
        }

        getTable(tableId) {
            return this.tables.get(tableId);
        }

        // Configurações pré-definidas para casos comuns
        getPresetConfig(preset) {
            const presets = {
                simple: {
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: true
                },
                
                minimal: {
                    dom: 't',
                    paging: false,
                    searching: false,
                    info: false
                },
                
                serverSide: {
                    serverSide: true,
                    processing: true,
                    deferRender: true
                },
                
                export: {
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                }
            };
            
            return presets[preset] || {};
        }

        // Utilitários
        addGlobalSearch(tableId, searchInputId) {
            const dataTable = this.tables.get(tableId);
            const searchInput = document.getElementById(searchInputId);
            
            if (dataTable && searchInput) {
                searchInput.addEventListener('keyup', function() {
                    dataTable.search(this.value).draw();
                });
            }
        }

        addColumnFilter(tableId, columnIndex, filterId) {
            const dataTable = this.tables.get(tableId);
            const filter = document.getElementById(filterId);
            
            if (dataTable && filter) {
                filter.addEventListener('change', function() {
                    dataTable.column(columnIndex).search(this.value).draw();
                });
            }
        }
    }

    // Inicialização global
    let dataTablesManager;
    
    if (typeof window !== 'undefined') {
        // Aguarda jQuery e DataTables estarem disponíveis
        function initWhenReady() {
            if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
                dataTablesManager = new DataTablesManager();
                window.dataTablesManager = dataTablesManager;
                window.DataTablesManager = DataTablesManager;
            } else {
                setTimeout(initWhenReady, 100);
            }
        }
        
        initWhenReady();
    }
})();