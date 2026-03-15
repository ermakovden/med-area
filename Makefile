	SHELL := bash
.ONESHELL:
.SHELLFLAGS := -eu -o pipefail -c
.DELETE_ON_ERROR:
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

.DEFAULT_GOAL := help

# --- Project ---
PROJECT ?= medarea

# --- Docker ---
COMPOSE      := docker compose
COMPOSE_PROD := -f docker-compose.yaml -f compose.production.yml
PHP_SERVICE  := php

# --- PHP ---
PHP     ?= php
ARTISAN := $(PHP) artisan

# --- Git ---
VERSION    ?= $(shell git describe --tags --always --dirty 2>/dev/null || echo "dev")
COMMIT     ?= $(shell git rev-parse --short HEAD 2>/dev/null || echo "unknown")
BUILD_TIME := $(shell date -u '+%Y-%m-%dT%H:%M:%SZ')

##@ Help

.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n"} \
		/^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-22s\033[0m %s\n", $$1, $$2} \
		/^##@/ {printf "\n\033[1m%s\033[0m\n", substr($$0, 5)}' $(MAKEFILE_LIST)

##@ Setup

.PHONY: install
install: ## Install PHP dependencies via Composer
	$(COMPOSE) exec $(PHP_SERVICE) composer install --no-interaction --prefer-dist --optimize-autoloader

.PHONY: setup
setup: ## First-time project setup: build containers, install deps, migrate
	$(COMPOSE) build
	$(COMPOSE) up -d
	@echo ""
	@echo "Waiting for services to be healthy..."
	@sleep 5
	$(COMPOSE) exec $(PHP_SERVICE) composer install --no-interaction --prefer-dist
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) migrate --seed
	@echo ""
	@echo "✅ Project setup complete! Web: http://localhost"

##@ Development

.PHONY: dev
dev: ## Start development environment (all services)
	$(COMPOSE) up -d
	@echo ""
	@echo "✅ Development environment started! Web: http://localhost"
	@echo "   Run migrations: make migrate"

.PHONY: build
build: ## Build Docker containers
	$(COMPOSE) build

.PHONY: stop
stop: ## Stop all running containers
	$(COMPOSE) down

.PHONY: restart
restart: ## Restart all containers
	$(COMPOSE) restart

.PHONY: logs
logs: ## Tail PHP container logs
	$(COMPOSE) logs -f $(PHP_SERVICE)

.PHONY: logs-worker
logs-worker: ## Tail worker container logs
	$(COMPOSE) logs -f worker

.PHONY: logs-all
logs-all: ## Tail all container logs
	$(COMPOSE) logs -f

.PHONY: shell
shell: ## Open bash shell in PHP container
	$(COMPOSE) exec $(PHP_SERVICE) bash

##@ Infrastructure

.PHONY: infra-up
infra-up: ## Start only infrastructure services (db + redis), without app
	$(COMPOSE) up -d db redis

.PHONY: infra-down
infra-down: ## Stop infrastructure services
	$(COMPOSE) stop db redis

##@ Database

.PHONY: migrate
migrate: ## Run database migrations
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) migrate

.PHONY: migrate-fresh
migrate-fresh: ## ⚠ Drop all tables and re-run migrations with seeds (DESTRUCTIVE)
	@echo "⚠️  WARNING: This will drop all tables and re-seed the database!"
	@read -p "Are you sure? [y/N] " confirm && [[ $$confirm == [yY] ]] || (echo "Aborted."; exit 1)
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) migrate:fresh --seed

.PHONY: migrate-rollback
migrate-rollback: ## Rollback the last migration batch
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) migrate:rollback

.PHONY: seed
seed: ## Seed the database
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) db:seed

.PHONY: tinker
tinker: ## Open Laravel Tinker REPL
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) tinker

##@ Queue & Cache

.PHONY: queue-restart
queue-restart: ## Gracefully restart queue workers
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) queue:restart

.PHONY: cache-clear
cache-clear: ## Clear all application caches (config, route, view, cache)
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) cache:clear
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) config:clear
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) route:clear
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) view:clear

.PHONY: optimize
optimize: ## Cache config, routes, and views for production performance
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) config:cache
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) route:cache
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) view:cache

##@ Testing

.PHONY: test
test: ## Run all tests
	$(COMPOSE) exec -T $(PHP_SERVICE) composer test

.PHONY: test-unit
test-unit: ## Run unit tests
	$(COMPOSE) exec -T $(PHP_SERVICE) $(ARTISAN) test tests/Unit

.PHONY: test-feature
test-feature: ## Run feature tests
	$(COMPOSE) exec -T $(PHP_SERVICE) $(ARTISAN) test tests/Feature

.PHONY: test-arch
test-arch: ## Run architecture tests
	$(COMPOSE) exec -T $(PHP_SERVICE) $(ARTISAN) test tests/Architecture

.PHONY: test-file
test-file: ## Run specific test file — usage: make test-file FILE=tests/Feature/MyTest.php
	$(COMPOSE) exec -T $(PHP_SERVICE) $(ARTISAN) test $(FILE)

.PHONY: test-elastic
test-elastic: ## Run Elasticsearch integration tests (requires running ELK stack)
	$(COMPOSE) exec -T $(PHP_SERVICE) composer test:elastic

.PHONY: test-coverage
test-coverage: ## Run tests with code coverage report (HTML output to coverage/)
	$(COMPOSE) exec $(PHP_SERVICE) $(PHP) -d error_reporting=E_ALL\&~E_DEPRECATED vendor/bin/phpunit --coverage-html coverage/

##@ Code Quality

.PHONY: cs-fix
cs-fix: ## Fix code style with PHP CS Fixer
	$(COMPOSE) exec $(PHP_SERVICE) git config --global --add safe.directory /var/www/app
	$(COMPOSE) exec $(PHP_SERVICE) bash -c 'php -d error_reporting=E_ALL\&~E_DEPRECATED $$(which composer) cs-fixer:fix'

.PHONY: phpstan
phpstan: ## Run PHPStan static analysis
	$(COMPOSE) exec $(PHP_SERVICE) $(PHP) -d error_reporting=E_ALL\&~E_DEPRECATED vendor/bin/phpstan analyse --memory-limit=2G --configuration=phpstan.neon

.PHONY: routes
routes: ## List all registered routes
	$(COMPOSE) exec $(PHP_SERVICE) $(ARTISAN) route:list

.PHONY: doc
doc: ## Generate OpenAPI documentation
	$(COMPOSE) exec $(PHP_SERVICE) composer doc

##@ CI

.PHONY: check
check: test cs-fix phpstan ## Run all quality checks (tests + cs-fix + phpstan)
	@echo ""
	@echo "✅ All checks passed!"

.PHONY: ci
ci: check ## Full CI pipeline (alias for check)

##@ Docker — Production

.PHONY: prod-build
prod-build: ## Build production images (multi-stage, optimised)
	$(COMPOSE) $(COMPOSE_PROD) build

.PHONY: prod-up
prod-up: ## Start production environment
	$(COMPOSE) $(COMPOSE_PROD) up -d
	@echo "✅ Production environment started!"

.PHONY: prod-down
prod-down: ## Stop production environment
	$(COMPOSE) $(COMPOSE_PROD) down

##@ Cleanup

.PHONY: clean
clean: ## ⚠ Stop containers and remove volumes (DESTRUCTIVE)
	$(COMPOSE) down -v
	@echo ""
	@echo "✅ Docker containers and volumes cleaned!"

.PHONY: clean-images
clean-images: ## Remove all local project Docker images
	$(COMPOSE) down --rmi local
