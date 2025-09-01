<div align="center">
  <img src="public/images/sps.svg" alt="SPS Logo" width="400">
</div>

# Spassu-Saber

Sistema de gerenciamento de biblioteca desenvolvido com Laravel 11 e Docker.

## Pré-requisitos

- Docker Desktop
- Git

## Setup Rápido

```bash
# Clone o repositório
git clone <repository-url>
cd sds-public

# Execute o setup completo
make setup
```

O comando `make setup` irá:
- Construir e iniciar os containers Docker
- Instalar dependências do Composer
- Configurar o banco de dados
- Executar as migrações
- Instalar dependências do NPM
- Compilar os assets

## Configuração da APP_KEY

Se você precisar gerar uma nova chave de aplicação:

```bash
# Copie o arquivo de exemplo
cp .env.example .env

# Gere a chave da aplicação
docker-compose exec laravel.test php artisan key:generate

# OU usando o Makefile
make shell
php artisan key:generate
```

A APP_KEY é essencial para criptografia e sessões do Laravel.

## Acesso à Aplicação

Após o setup:
- **Aplicação Web**: http://localhost
- **API**: http://localhost/api/v1
- **Banco de dados**: MySQL na porta 3306

## Comandos Úteis

```bash
# Iniciar os serviços
make up

# Parar os serviços
make down

# Executar testes
make test

# Acessar o container da aplicação
make shell

# Ver logs
make logs

# Rebuild completo
make rebuild
```

## Tecnologias

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Blade Templates, Bootstrap 5, JavaScript
- **Banco de Dados**: MySQL 8.0
- **Containerização**: Docker, Laravel Sail
- **Build Tools**: Vite, NPM

## Estrutura da API

A API segue o padrão REST e está disponível em `/api/v1`:

- `GET /api/v1/books` - Listar livros
- `POST /api/v1/books` - Criar livro
- `GET /api/v1/books/{id}` - Obter livro
- `PUT /api/v1/books/{id}` - Atualizar livro
- `DELETE /api/v1/books/{id}` - Deletar livro
- `GET /api/v1/authors-options` - Listar autores
- `GET /api/v1/subjects-options` - Listar assuntos
- `GET /api/v1/reports/summary` - Relatório resumo

## Desenvolvimento

Este projeto utiliza Laravel Sail para um ambiente de desenvolvimento consistente. Todos os comandos são executados através do Makefile para simplificar o workflow.

Para mais detalhes sobre a API, consulte a documentação OpenAPI disponível na aplicação.

