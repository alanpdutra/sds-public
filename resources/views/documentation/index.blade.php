@extends('layouts.app')

@section('title', 'Documentação das APIs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Documentação das APIs - Sistema de Gerenciamento de Livros</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" id="v-pills-overview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-overview" type="button" role="tab">Visão Geral</button>
                                <button class="nav-link" id="v-pills-books-tab" data-bs-toggle="pill" data-bs-target="#v-pills-books" type="button" role="tab">Livros</button>
                                <button class="nav-link" id="v-pills-authors-tab" data-bs-toggle="pill" data-bs-target="#v-pills-authors" type="button" role="tab">Autores</button>
                                <button class="nav-link" id="v-pills-subjects-tab" data-bs-toggle="pill" data-bs-target="#v-pills-subjects" type="button" role="tab">Assuntos</button>
                                <button class="nav-link" id="v-pills-reports-tab" data-bs-toggle="pill" data-bs-target="#v-pills-reports" type="button" role="tab">Relatórios</button>
                                <button class="nav-link" id="v-pills-responses-tab" data-bs-toggle="pill" data-bs-target="#v-pills-responses" type="button" role="tab">Formatos de Resposta</button>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content" id="v-pills-tabContent">
                                <!-- Visão Geral -->
                                <div class="tab-pane fade show active" id="v-pills-overview" role="tabpanel">
                                    <h5>Visão Geral da API</h5>
                                    <p>Esta documentação descreve as APIs REST do Sistema de Gerenciamento de Livros.</p>
                                    
                                    <h6>Base URL</h6>
                                    <code>{{ url('/api/v1') }}</code>
                                    
                                    <h6>Formato de Dados</h6>
                                    <p>Todas as requisições e respostas utilizam JSON.</p>
                                    
                                    <h6>Headers Obrigatórios</h6>
                                    <pre><code>Content-Type: application/json
Accept: application/json</code></pre>
                                </div>

                                <!-- Livros -->
                                <div class="tab-pane fade" id="v-pills-books" role="tabpanel">
                                    <h5>API de Livros</h5>
                                    
                                    <div class="accordion" id="booksAccordion">
                                        <!-- GET /books -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#books-index">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/books - Listar livros
                                                </button>
                                            </h2>
                                            <div id="books-index" class="accordion-collapse collapse show" data-bs-parent="#booksAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Lista livros com paginação e filtros opcionais.</p>
                                                    
                                                    <h6>Parâmetros de Query (opcionais):</h6>
                                                    <ul>
                                                        <li><code>titulo</code> - Filtrar por título</li>
                                                        <li><code>autor</code> - Filtrar por nome do autor</li>
                                                        <li><code>assunto</code> - Filtrar por descrição do assunto</li>
                                                        <li><code>per_page</code> - Itens por página (padrão: 10)</li>
                                                    </ul>
                                                    
                                                    <h6>Exemplo de Resposta:</h6>
                                                    <pre><code>{
  "success": true,
  "data": [
    {
      "CodL": 1,
      "Titulo": "Dom Casmurro",
      "Editora": "Ática",
      "Edicao": 1,
      "AnoPublicacao": 1899,
      "Valor": "25.90",
      "authors": [...],
      "subjects": [...]
    }
  ],
  "pagination": {
    "page": 1,
    "pages": 5,
    "total": 50
  }
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- POST /books -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#books-store">
                                                    <span class="badge bg-primary me-2">POST</span> /api/v1/books - Criar livro
                                                </button>
                                            </h2>
                                            <div id="books-store" class="accordion-collapse collapse" data-bs-parent="#booksAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Cria um novo livro.</p>
                                                    
                                                    <h6>Campos Obrigatórios:</h6>
                                                    <ul>
                                                        <li><code>Titulo</code> - String, máximo 40 caracteres</li>
                                                        <li><code>Editora</code> - String, máximo 40 caracteres</li>
                                                        <li><code>Edicao</code> - Integer, mínimo 1</li>
                                                        <li><code>AnoPublicacao</code> - Integer, entre 1500 e ano atual</li>
                                                    </ul>
                                                    
                                                    <h6>Campos Opcionais:</h6>
                                                    <ul>
                                                        <li><code>Valor</code> - Numeric, mínimo 0</li>
                                                        <li><code>authors</code> - Array de IDs de autores</li>
                                                        <li><code>subjects</code> - Array de IDs de assuntos</li>
                                                    </ul>
                                                    
                                                    <h6>Exemplo de Requisição:</h6>
                                                    <pre><code>{
  "Titulo": "O Cortiço",
  "Editora": "Ática",
  "Edicao": 1,
  "AnoPublicacao": 1890,
  "Valor": "R$ 29,90",
  "authors": [1, 2],
  "subjects": [1]
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- GET /books/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#books-show">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/books/{id} - Obter livro
                                                </button>
                                            </h2>
                                            <div id="books-show" class="accordion-collapse collapse" data-bs-parent="#booksAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Obtém um livro específico com autores e assuntos.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do livro</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PUT /books/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#books-update">
                                                    <span class="badge bg-warning me-2">PUT</span> /api/v1/books/{id} - Atualizar livro
                                                </button>
                                            </h2>
                                            <div id="books-update" class="accordion-collapse collapse" data-bs-parent="#booksAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Atualiza um livro existente.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do livro</p>
                                                    <p><strong>Campos:</strong> Mesmos campos do POST, todos opcionais.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DELETE /books/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#books-delete">
                                                    <span class="badge bg-danger me-2">DELETE</span> /api/v1/books/{id} - Excluir livro
                                                </button>
                                            </h2>
                                            <div id="books-delete" class="accordion-collapse collapse" data-bs-parent="#booksAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Exclui um livro.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do livro</p>
                                                    <p><strong>Resposta:</strong> 204 No Content (sem corpo)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Autores -->
                                <div class="tab-pane fade" id="v-pills-authors" role="tabpanel">
                                    <h5>API de Autores</h5>
                                    
                                    <div class="accordion" id="authorsAccordion">
                                        <!-- GET /authors -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#authors-index">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/authors - Listar autores
                                                </button>
                                            </h2>
                                            <div id="authors-index" class="accordion-collapse collapse show" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Lista autores com paginação.</p>
                                                    
                                                    <h6>Parâmetros de Query (opcionais):</h6>
                                                    <ul>
                                                        <li><code>nome</code> - Filtrar por nome</li>
                                                        <li><code>per_page</code> - Itens por página (padrão: 10)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- POST /authors -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#authors-store">
                                                    <span class="badge bg-primary me-2">POST</span> /api/v1/authors - Criar autor
                                                </button>
                                            </h2>
                                            <div id="authors-store" class="accordion-collapse collapse" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Cria um novo autor.</p>
                                                    
                                                    <h6>Campos Obrigatórios:</h6>
                                                    <ul>
                                                        <li><code>Nome</code> - String, máximo 40 caracteres, único</li>
                                                    </ul>
                                                    
                                                    <h6>Exemplo de Requisição:</h6>
                                                    <pre><code>{
  "Nome": "Machado de Assis"
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- GET /authors/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#authors-show">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/authors/{id} - Obter autor
                                                </button>
                                            </h2>
                                            <div id="authors-show" class="accordion-collapse collapse" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Obtém um autor específico.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do autor</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PUT /authors/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#authors-update">
                                                    <span class="badge bg-warning me-2">PUT</span> /api/v1/authors/{id} - Atualizar autor
                                                </button>
                                            </h2>
                                            <div id="authors-update" class="accordion-collapse collapse" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Atualiza um autor existente.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do autor</p>
                                                    <p><strong>Campos:</strong> <code>Nome</code> (opcional)</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DELETE /authors/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#authors-delete">
                                                    <span class="badge bg-danger me-2">DELETE</span> /api/v1/authors/{id} - Excluir autor
                                                </button>
                                            </h2>
                                            <div id="authors-delete" class="accordion-collapse collapse" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Exclui um autor.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do autor</p>
                                                    <p><strong>Observação:</strong> Não é possível excluir autores que possuem livros associados.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- GET /authors-options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#authors-options">
                                                    <span class="badge bg-info me-2">GET</span> /api/v1/authors-options - Opções de autores
                                                </button>
                                            </h2>
                                            <div id="authors-options" class="accordion-collapse collapse" data-bs-parent="#authorsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Retorna todos os autores para uso em selects.</p>
                                                    <p><strong>Resposta:</strong> Lista simples com CodAu e Nome.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assuntos -->
                                <div class="tab-pane fade" id="v-pills-subjects" role="tabpanel">
                                    <h5>API de Assuntos</h5>
                                    
                                    <div class="accordion" id="subjectsAccordion">
                                        <!-- GET /subjects -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-index">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/subjects - Listar assuntos
                                                </button>
                                            </h2>
                                            <div id="subjects-index" class="accordion-collapse collapse show" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Lista assuntos com paginação.</p>
                                                    
                                                    <h6>Parâmetros de Query (opcionais):</h6>
                                                    <ul>
                                                        <li><code>descricao</code> - Filtrar por descrição</li>
                                                        <li><code>per_page</code> - Itens por página (padrão: 10)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- POST /subjects -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-store">
                                                    <span class="badge bg-primary me-2">POST</span> /api/v1/subjects - Criar assunto
                                                </button>
                                            </h2>
                                            <div id="subjects-store" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Cria um novo assunto.</p>
                                                    
                                                    <h6>Campos Obrigatórios:</h6>
                                                    <ul>
                                                        <li><code>Descricao</code> - String, máximo 20 caracteres, único</li>
                                                    </ul>
                                                    
                                                    <h6>Exemplo de Requisição:</h6>
                                                    <pre><code>{
  "Descricao": "Literatura"
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- GET /subjects/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-show">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/subjects/{id} - Obter assunto
                                                </button>
                                            </h2>
                                            <div id="subjects-show" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Obtém um assunto específico.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do assunto</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PUT /subjects/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-update">
                                                    <span class="badge bg-warning me-2">PUT</span> /api/v1/subjects/{id} - Atualizar assunto
                                                </button>
                                            </h2>
                                            <div id="subjects-update" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Atualiza um assunto existente.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do assunto</p>
                                                    <p><strong>Campos:</strong> <code>Descricao</code> (opcional)</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DELETE /subjects/{id} -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-delete">
                                                    <span class="badge bg-danger me-2">DELETE</span> /api/v1/subjects/{id} - Excluir assunto
                                                </button>
                                            </h2>
                                            <div id="subjects-delete" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Exclui um assunto.</p>
                                                    <p><strong>Parâmetro:</strong> <code>id</code> - ID do assunto</p>
                                                    <p><strong>Observação:</strong> Não é possível excluir assuntos que possuem livros associados.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- GET /subjects-options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subjects-options">
                                                    <span class="badge bg-info me-2">GET</span> /api/v1/subjects-options - Opções de assuntos
                                                </button>
                                            </h2>
                                            <div id="subjects-options" class="accordion-collapse collapse" data-bs-parent="#subjectsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Retorna todos os assuntos para uso em selects.</p>
                                                    <p><strong>Resposta:</strong> Lista simples com CodAs e Descricao.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Relatórios -->
                                <div class="tab-pane fade" id="v-pills-reports" role="tabpanel">
                                    <h5>API de Relatórios</h5>
                                    
                                    <div class="accordion" id="reportsAccordion">
                                        <!-- GET /reports/summary -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reports-summary">
                                                    <span class="badge bg-success me-2">GET</span> /api/v1/reports/summary - Relatório resumo
                                                </button>
                                            </h2>
                                            <div id="reports-summary" class="accordion-collapse collapse show" data-bs-parent="#reportsAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Descrição:</strong> Retorna dados do relatório de autores com quantidade de livros.</p>
                                                    
                                                    <h6>Exemplo de Resposta:</h6>
                                                    <pre><code>{
  "success": true,
  "data": [
    {
      "autor": "Machado de Assis",
      "quantidade_livros": 5
    },
    {
      "autor": "José de Alencar",
      "quantidade_livros": 3
    }
  ]
}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formatos de Resposta -->
                                <div class="tab-pane fade" id="v-pills-responses" role="tabpanel">
                                    <h5>Formatos de Resposta</h5>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <h6>Resposta de Sucesso</h6>
                                            <pre><code>{
  "success": true,
  "data": { ... },
  "pagination": { // apenas em listagens
    "page": 1,
    "pages": 5,
    "total": 50
  }
}</code></pre>
                                            
                                            <h6>Erro de Validação (422)</h6>
                                            <pre><code>{
  "success": false,
  "error": {
    "type": "VALIDATION_ERROR",
    "message": "Dados inválidos",
    "fields": {
      "Nome": ["Nome é obrigatório."]
    }
  }
}</code></pre>
                                            
                                            <h6>Recurso Não Encontrado (404)</h6>
                                            <pre><code>{
  "success": false,
  "error": {
    "type": "NOT_FOUND",
    "message": "Recurso não encontrado"
  }
}</code></pre>
                                            
                                            <h6>Códigos de Status HTTP</h6>
                                            <ul>
                                                <li><code>200</code> - OK (sucesso)</li>
                                                <li><code>201</code> - Created (criado com sucesso)</li>
                                                <li><code>204</code> - No Content (excluído com sucesso)</li>
                                                <li><code>404</code> - Not Found (não encontrado)</li>
                                                <li><code>422</code> - Unprocessable Entity (erro de validação)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection