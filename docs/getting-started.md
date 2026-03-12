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

# 2. Start all services (nginx, php, postgres, redis, worker, scheduler)
docker-compose up -d

# 3. Generate application key
docker-compose exec php php artisan key:generate

# 4. Run database migrations
docker-compose exec php php artisan migrate
```

API is available at `http://localhost:80` (or `APP_WEB_PORT` from your `.env`).

## Makefile Commands

The project includes a `Makefile` for common tasks:

| Command | Description |
|---------|-------------|
| `make dev` | Start development environment |
| `make build` | Build Docker containers |
| `make clean` | Remove containers and volumes |
| `make test` | Run all tests |
| `make test-unit` | Run unit tests only |
| `make test-feature` | Run feature tests only |
| `make test-arch` | Run architecture integrity tests |
| `make test-file FILE=tests/Feature/MyTest.php` | Run a specific test file |
| `make cs-fix` | Fix code style with PHP CS Fixer |
| `make phpstan` | Run PHPStan static analysis (level 8) |
| `make check` | Run all checks: tests + cs-fix + phpstan |

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
