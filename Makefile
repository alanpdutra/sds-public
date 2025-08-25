# SDS Library - Makefile
# Comandos para gerenciar o ambiente Docker

.PHONY: help up down restart logs shell install setup build clean test format analyse

# Comando padrão
help:
	@echo "SDS Library - Comandos disponíveis:"
	@echo ""
	@echo "  make up        - Sobe os containers Docker"
	@echo "  make down      - Para os containers Docker"
	@echo "  make restart   - Reinicia os containers"
	@echo "  make logs      - Mostra logs do Laravel"
	@echo "  make shell     - Acessa o container Laravel"
	@echo "  make install   - Instala dependências PHP"
	@echo "  make setup     - Setup completo da aplicação"
	@echo "  make build     - Compila assets do frontend"
	@echo "  make clean     - Remove containers e volumes"
	@echo "  make test      - Executa testes"
	@echo "  make format    - Formata código (Pint)"
	@echo "  make analyse   - Análise estática (Larastan)"
	@echo ""

# Comandos principais
up:
	@echo "🚀 Subindo containers..."
	docker-compose up -d
	@echo "✅ Containers iniciados!"
	@echo "📱 Frontend: http://localhost:8076"
	@echo "🔗 API: http://localhost:8076/api/v1"

down:
	@echo "🛑 Parando containers..."
	docker-compose down
	@echo "✅ Containers parados!"

restart: down up

logs:
	docker-compose logs -f laravel.test

shell:
	docker-compose exec laravel.test bash

# Setup e instalação
install:
	@echo "📦 Instalando dependências PHP..."
	docker-compose exec laravel.test composer install
	@echo "✅ Dependências instaladas!"

setup:
	@echo "🔧 Configurando aplicação..."
	@if [ ! -f .env ]; then \
		echo "📋 Copiando .env.example para .env..."; \
		cp .env.example .env; \
	fi
	@echo "🗑️ Removendo banco existente..."
	docker-compose down -v
	docker volume rm sds_sail-mysql || true
	make up
	@echo "⏳ Aguardando MySQL inicializar..."
	docker-compose up -d --wait
	@echo "✅ MySQL pronto!"
	make install
	@echo "🔑 Gerando chave da aplicação..."
	docker-compose exec laravel.test php artisan key:generate
	@echo "🗄️ Executando migrations e seeders..."
	docker-compose exec laravel.test php artisan migrate:fresh --seed
	@echo "📦 Instalando dependências do frontend..."
	npm install
	make build
	@echo "🎉 Setup completo!"
	@echo "📱 Acesse: http://localhost:8076"

# Frontend
build:
	@echo "🏗️ Compilando assets..."
	npm run build
	@echo "✅ Assets compilados!"

dev:
	@echo "🔥 Iniciando modo desenvolvimento..."
	npm run dev

# Limpeza
clean:
	@echo "🧹 Removendo containers e volumes..."
	docker-compose down -v
	docker system prune -f
	@echo "✅ Limpeza concluída!"

# Qualidade de código
test:
	docker-compose exec laravel.test php artisan test

format:
	docker-compose exec laravel.test composer format

analyse:
	docker-compose exec laravel.test composer analyse

# Cache
cache-clear:
	docker-compose exec laravel.test php artisan cache:clear
	docker-compose exec laravel.test php artisan config:clear
	docker-compose exec laravel.test php artisan view:clear

# Banco de dados
db-fresh:
	docker-compose exec laravel.test php artisan migrate:fresh --seed

db-reset: down
	docker volume rm sds_sail-mysql || true
	make up
	sleep 10
	make db-fresh