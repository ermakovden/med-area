[Back to README](../README.md) · [Architecture →](architecture.md)

# Getting Started

## Prerequisites

| Tool | Version |
|------|---------|
| Docker & Docker Compose | Latest |
| PHP | 8.5+ (local dev only) |
| Composer | Latest (local dev only) |
| Node.js | 20+ (local dev only) |
| Make | Optional — for Makefile shortcuts |

## Docker Setup

```bash
# 1. Copy environment config
cp .env.example .env

# 2. First-time setup: build containers, install deps, run migrations
make setup
```

Or step by step:

```bash
docker compose up -d
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
```

API is available at `http://localhost:80` (or `APP_WEB_PORT` from your `.env`).

## Makefile Commands

Run `make help` to see the full list. Common commands by category:

### Setup

| Command | Description |
|---------|-------------|
| `make setup` | First-time setup: build containers, install deps, migrate |
| `make install` | Install PHP dependencies via Composer |
| `make build` | Build Docker containers |

### Running

| Command | Description |
|---------|-------------|
| `make dev` | Start all services |
| `make stop` | Stop all containers |
| `make restart` | Restart all containers |
| `make infra-up` | Start only DB + Redis (no app) |
| `make infra-down` | Stop infrastructure services |
| `make shell` | Open bash shell in PHP container |
| `make tinker` | Open Laravel Tinker REPL |

### Logs

| Command | Description |
|---------|-------------|
| `make logs` | Tail PHP container logs |
| `make logs-worker` | Tail worker container logs |
| `make logs-all` | Tail all container logs |

### Database

| Command | Description |
|---------|-------------|
| `make migrate` | Run database migrations |
| `make migrate-fresh` | ⚠ Drop all tables and re-run migrations with seeds |
| `make migrate-rollback` | Rollback the last migration batch |
| `make seed` | Seed the database |

### Testing & Quality

| Command | Description |
|---------|-------------|
| `make test` | Run all tests |
| `make test-unit` | Unit tests only |
| `make test-feature` | Feature tests only |
| `make test-arch` | Architecture integrity tests |
| `make test-file FILE=path` | Run a specific test file |
| `make test-elastic` | Elasticsearch integration tests (requires ELK stack) |
| `make test-coverage` | Tests with HTML coverage report (`coverage/`) |
| `make cs-fix` | Fix code style with PHP CS Fixer |
| `make phpstan` | PHPStan static analysis (level 8) |
| `make check` | All quality checks: tests + cs-fix + phpstan |
| `make ci` | Full CI pipeline (alias for `check`) |

### Other

| Command | Description |
|---------|-------------|
| `make routes` | List all registered routes |
| `make doc` | Generate OpenAPI documentation |
| `make cache-clear` | Clear config, route, view, and app caches |
| `make optimize` | Cache config/routes/views for production |
| `make kibana-import` | Re-import Kibana dashboards |
| `make prod-build` | Build production Docker images |
| `make prod-up` | Start production environment |
| `make prod-down` | Stop production environment |
| `make clean` | ⚠ Stop containers and remove volumes |
| `make clean-images` | Remove all local project Docker images |

## Composer Scripts

```bash
composer test             # Run PHPUnit tests
composer cs-fixer:fix     # Fix code style
composer phpstan          # Static analysis
composer doc              # Generate OpenAPI documentation
```

## NPM Scripts

```bash
npm run dev     # Start Vite development server
npm run build   # Build production assets
```

## See Also

- [Configuration](configuration.md) — Environment variables and `.env` setup
- [Development](development.md) — Coding standards and testing workflow
- [Architecture](architecture.md) — Project structure overview
