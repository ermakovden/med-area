# Med Area

> RESTful API platform for medical analysis processing, built with Laravel 12 and DDD architecture.

MedArea allows users to upload medical documents, process them via Yandex Cloud Vision OCR, and manage analysis results through a clean, layered API.

## Quick Start

```bash
cp .env.example .env
docker-compose up -d
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
```

API available at `http://localhost:80` (or configured `APP_WEB_PORT`).

## Key Features

- **JWT Authentication** — register, login, token refresh via `tymon/jwt-auth`
- **Medical Analysis Management** — CRUD for patient analysis records
- **OCR Recognition** — file upload + Yandex Cloud Vision text extraction
- **Async Processing** — AI jobs dispatched to Redis-backed Laravel Queue
- **S3 Storage** — files stored in Yandex Cloud S3-compatible storage
- **Strict DDD Architecture** — Domain / Application / Infrastructure / Presentation layers

## Documentation

| Guide | Description |
|-------|-------------|
| [Getting Started](docs/getting-started.md) | Prerequisites, Docker setup, Makefile commands |
| [Architecture](docs/architecture.md) | DDD layer structure and dependency rules |
| [Development](docs/development.md) | Coding standards, testing, static analysis, CI |
| [Configuration](docs/configuration.md) | Environment variables, database, packages |
| [ELK Setup](docs/elk-setup.md) | Centralized logging with Elasticsearch, Kibana, Logstash, Filebeat |

## License

MIT
