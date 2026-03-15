[← Development](development.md) · [Back to README](../README.md) · [ELK Setup →](elk-setup.md)

# Configuration

## Environment Variables

Copy `.env.example` to `.env` and fill in the values below.

### Application

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Environment (`local`, `production`) | `local` |
| `APP_DEBUG` | Enable debug mode | `true` |
| `APP_URL` | Application base URL | `http://localhost` |
| `APP_WEB_PORT` | Nginx port exposed on host | `80` |

### Database (PostgreSQL)

| Variable | Description |
|----------|-------------|
| `DB_CONNECTION` | Driver (`pgsql`) |
| `DB_HOST` | PostgreSQL host |
| `DB_PORT` | PostgreSQL port (default `5432`) |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |

> Tests use SQLite in-memory — set in `phpunit.xml`, no `.env` changes needed.

### Authentication

| Variable | Description |
|----------|-------------|
| `JWT_SECRET` | Secret key for JWT token signing — generate with `php artisan jwt:secret` |

### Cache & Queue (Redis)

| Variable | Description |
|----------|-------------|
| `REDIS_HOST` | Redis host |
| `REDIS_PORT` | Redis port (default `6379`) |
| `CACHE_DRIVER` | Cache backend (`redis`) |
| `QUEUE_CONNECTION` | Queue backend (`redis`) |

### S3 Storage (Yandex Cloud)

| Variable | Description |
|----------|-------------|
| `AWS_ACCESS_KEY_ID` | Yandex Cloud service account key ID |
| `AWS_SECRET_ACCESS_KEY` | Yandex Cloud secret key |
| `AWS_DEFAULT_REGION` | Region (e.g. `ru-central1`) |
| `AWS_BUCKET` | S3 bucket name |
| `AWS_ENDPOINT` | Yandex Cloud S3 endpoint URL |

### Yandex Cloud OCR (Recogniser)

| Variable | Description |
|----------|-------------|
| `RECOGNISER_YC_API_KEY` | Yandex Cloud Vision API key |
| `RECOGNISER_YC_FOLDER_ID` | Yandex Cloud folder ID |
| `RECOGNISER_YC_API_URL` | Vision OCR endpoint URL |

## Database

- **Driver:** PostgreSQL (via Docker)
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`
- **Testing:** SQLite in-memory (configured in `phpunit.xml`)

## Key Packages

### Production

| Package | Purpose |
|---------|---------|
| `laravel/framework` ^12.0 | Core framework |
| `spatie/laravel-data` ^4.17 | Data transfer objects |
| `tymon/jwt-auth` ^2.2 | JWT authentication |
| `league/flysystem-aws-s3-v3` ^3.0 | S3 filesystem driver |
| `predis/predis` ^3.2 | Redis client |

### Development

| Package | Purpose |
|---------|---------|
| `phpunit/phpunit` ^11.5.3 | Testing framework |
| `larastan/larastan` ^3.7 | Laravel-specific PHPStan rules |
| `friendsofphp/php-cs-fixer` ^3.88 | Code style fixer |
| `zircote/swagger-php` ^5.4 | OpenAPI doc generator |
| `barryvdh/laravel-ide-helper` ^3.6 | IDE autocompletion |

## See Also

- [Getting Started](getting-started.md) — Docker setup and first run
- [Development](development.md) — Testing and CI workflow
- [Architecture](architecture.md) — Project structure
