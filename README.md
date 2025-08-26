# SDS Library – API-first

Minimal Laravel 12 API-first refactor.

## Architecture
- API-first: Blade pages fetch data via `/api/v1`.
- Code in English; DB tables/columns remain in Portuguese.
- Web controllers render Blade only. No business logic/queries.
- Services handle create/update/delete and pagination.
- Requests validate and normalize inputs (e.g., money).
- Resources return only existing DB fields.

## API
Base: `/api/v1`

Success
```
{ "success": true, "data": <resource|list>, "pagination"?: { "page": n, "pages": n, "total": n } }
```

422
```
{ "success": false, "error": { "type": "VALIDATION_ERROR", "message": "Dados inválidos", "fields": { Campo: [mensagens...] } } }
```

404
```
{ "success": false, "error": { "type": "NOT_FOUND", "message": "Recurso não encontrado" } }
```

Endpoints
- `GET /books` – list with optional filters `titulo`, `autor`, `assunto`
- `POST /books` – create
- `GET /books/{id}` – get
- `PUT /books/{id}` – update
- `DELETE /books/{id}` – delete
- `GET /authors-options` – list authors (CodAu, Nome)
- `GET /subjects-options` – list subjects (CodAs, Descricao)
- `GET /reports/summary` – { booksTotal, authorsTotal, subjectsTotal, latestBooks[], updatedAt }

## Web Pages
- `GET /books` – index (fetch + pagination)
- `GET /books/create` – create (single submit handler via fetch)
- `GET /books/{id}/edit` – edit (fetch + update)
- `GET /reports` – summary + export CSV (client-side)

## Frontend
- Toasts/overlay already wired in layout. No alerts.
- `resources/js/masks.js`: applyMoneyMask, applyYearMask, removeMoney, formatMoney.
- `public/js/form-utils.js`: setFieldError, clearFieldError, syncBootstrapValidity, buttonLoadingOn/Off, validateClientSide, applyBlurRealtime.
- `public/js/api-client.js`: wrapper for `/api/v1` with helpers for books/authors/subjects/reports.

## Quality
- Pint, Larastan, Pest configured.

Composer scripts
- `composer format` – Pint
- `composer analyse` – Larastan (phpstan)
- `composer test` – Pest

## Setup da Aplicação

### Pré-requisitos
- Docker e Docker Compose
- Git

### Passo a Passo

#### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd sds
```

#### 2. Configure as variáveis de ambiente
```bash
cp .env.example .env
```

Edite o arquivo `.env` e configure as seguintes variáveis para Docker:
```env
APP_NAME="SDS Library"
APP_ENV=local
APP_KEY=base64:SEU_APP_KEY_AQUI
APP_DEBUG=true
APP_URL=http://localhost:8076

# Database (MySQL via Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sds
DB_USERNAME=sail
DB_PASSWORD=password

# Portas Docker
APP_PORT=8076
VITE_PORT=5173
FORWARD_DB_PORT=3307

# Usuário Docker (ajuste conforme seu sistema)
WWWUSER=1000
WWWGROUP=1000
```

#### 3. Suba os containers Docker
```bash
# Usando Docker Compose diretamente
docker-compose up -d

# OU usando Makefile (recomendado)
make up
```

#### 4. Instale as dependências PHP
```bash
docker-compose exec laravel.test composer install
```

#### 5. Gere a chave da aplicação
```bash
docker-compose exec laravel.test php artisan key:generate
```

#### 6. Execute as migrations e seeders
```bash
docker-compose exec laravel.test php artisan migrate:fresh --seed
```

#### 7. Instale as dependências do frontend
```bash
npm install
```

#### 8. Compile os assets do frontend
```bash
npm run build
```

#### 9. Acesse a aplicação
- **Frontend**: http://localhost:8076
- **API**: http://localhost:8076/api/v1
- **MySQL**: localhost:3307

### Desenvolvimento

Para desenvolvimento com hot-reload do Vite:
```bash
npm run dev
# OU
make dev
```

### Comandos Makefile

O projeto inclui um Makefile para simplificar operações comuns:

```bash
# Ver todos os comandos disponíveis
make help

# Comandos principais
make up          # Sobe os containers
make down        # Para os containers
make restart     # Reinicia os containers
make setup       # Setup completo da aplicação

# Desenvolvimento
make logs        # Mostra logs do Laravel
make shell       # Acessa o container Laravel
make build       # Compila assets do frontend
make dev         # Modo desenvolvimento (Vite)

# Qualidade de código
make test        # Executa testes
make format      # Formata código (Pint)
make analyse     # Análise estática (Larastan)

# Utilitários
make clean       # Remove containers e volumes
make cache-clear # Limpa cache do Laravel
make db-fresh    # Recria banco com seeders
make db-reset    # Reset completo do banco
```

### Comandos Úteis

#### Docker
```bash
# Usando Docker Compose
docker-compose down              # Parar containers
docker-compose logs laravel.test # Ver logs
docker-compose exec laravel.test bash # Acessar container
docker-compose up -d --build     # Rebuild containers

# Usando Makefile (mais simples)
make down        # Parar containers
make logs        # Ver logs
make shell       # Acessar container
make restart     # Reiniciar containers
make clean       # Limpeza completa
```

#### Laravel
```bash
# Executar comandos Artisan
docker-compose exec laravel.test php artisan <comando>

# Limpar cache
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan view:clear
```

### Troubleshooting

#### Erro de permissão
```bash
sudo chown -R $USER:$USER .
sudo chmod -R 755 storage bootstrap/cache
```

#### Erro ViteException (masks.js não encontrado)
Se aparecer erro relacionado ao `masks.js`:
```bash
npm run build
```

#### Erro de conexão com banco
Verifique se o MySQL está rodando:
```bash
docker-compose ps mysql
```

#### Reset completo
```bash
# Usando Docker Compose
docker-compose down -v
docker-compose up -d
docker-compose exec laravel.test composer install
docker-compose exec laravel.test php artisan key:generate
docker-compose exec laravel.test php artisan migrate:fresh --seed
npm install
npm run build

# Usando Makefile (mais simples)
make clean
make setup
```

## Conventions
- PSR-12 across PHP.
- Code in English; DB in PT-BR.
- UI/validation messages in PT-BR (curtas e claras).

