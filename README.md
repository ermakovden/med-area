# Med Area

**MedArea** is a Laravel 12-based pet project designed as a RESTful API platform for medical analysis processing. The application features a **Domain-Driven Design (DDD)** architecture with clear separation of concerns across domain, application, infrastructure, and presentation layers.

### Core Features
- User authentication and registration (JWT-based)
- Medical analysis management
- File recognition via OCR (Yandex Cloud integration)
- AI-powered processing capabilities
- S3-compatible storage integration (Yandex Cloud Storage)

### Technology Stack
- **Backend:** PHP 8.3+, Laravel 12
- **Database:** PostgreSQL
- **Cache/Queue:** Redis
- **Frontend Build:** Vite 7 + Tailwind CSS 4
- **Containerization:** Docker (nginx, PHP-FPM, PostgreSQL, Redis, worker, scheduler)
- **Authentication:** JWT (tymon/jwt-auth)
- **Data Transfer:** Spatie Laravel Data

## Building and Running

### Prerequisites
- Docker & Docker Compose
- PHP 8.3+ (for local development without Docker)
- Composer
- Node.js 20+

### Docker Setup

1. **Clone and configure environment:**
   ```bash
   cp .env.example .env
   ```

2. **Generate application key:**
   ```bash
   docker-compose exec php php artisan key:generate
   ```

3. **Start all services:**
   ```bash
   docker-compose up -d
   ```

4. **Run migrations:**
   ```bash
   docker-compose exec php php artisan migrate
   ```

5. **Access the application:**
   - Web: `http://localhost:80` (or configured `APP_WEB_PORT`)

### Key Composer Scripts

| Command | Description |
|---------|-------------|
| `composer test` | Run PHPUnit tests |
| `composer cs-fixer:fix` | Fix code style with PHP CS Fixer |
| `composer phpstan` | Run static analysis (PHPStan level 8) |
| `composer doc` | Generate OpenAPI documentation |

### Key NPM Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Start Vite development server |
| `npm run build` | Build production assets |

## Development Conventions

### Coding Standards
- **Style Guide:** PER-CS (PHP-FIG Extended Coding Style)
- **PHP Version:** PHP 8.3+ features allowed
- **Tooling:** PHP CS Fixer (`@PER-CS`, `@PHP82Migration` rules)
- **Enforcement:** Run `composer cs-fixer:fix` before commits

### Static Analysis
- **Tool:** PHPStan with Larastan
- **Level:** 8 (strict)
- **Scope:** `src/` directory
- **Run:** `composer phpstan`

### Testing Practices
- **Framework:** PHPUnit 11
- **Test Suites:**
  - `Architecture` - Architectural integrity tests
  - `Unit` - Unit tests for isolated components
  - `Feature` - Feature tests for HTTP endpoints
- **Configuration:** Tests run with SQLite in-memory database
- **Run all tests:** `composer test`

### Architecture Rules (inferred from tests)
- Domain layer should not depend on Application or Infrastructure
- Application layer depends only on Domain
- Infrastructure implements Domain interfaces
- Presentation layer uses Application services
- Strict layer separation enforced via architecture tests

### API Documentation
- **Format:** OpenAPI 3.0 (YAML)
- **Generation:** `composer doc` (uses swagger-php)
- **File:** `openapi.yaml`

### Environment Configuration
Key environment variables in `.env`:
- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `JWT_SECRET` - For JWT authentication
- `REDIS_HOST`, `REDIS_PORT` - For cache/queue
- `AWS_*` - Yandex Cloud Storage configuration
- `RECOGNISER_YC_*` - Yandex Cloud OCR API configuration

## Database

- **Driver:** PostgreSQL (via Docker)
- **Testing:** SQLite in-memory
- **Migrations:** Located in `database/migrations/`
- **Seeders:** Located in `database/seeders/`

### Production
- `laravel/framework` ^12.0 - Core framework
- `spatie/laravel-data` ^4.17 - Data transfer objects
- `tymon/jwt-auth` ^2.2 - JWT authentication
- `league/flysystem-aws-s3-v3` ^3.0 - S3 filesystem
- `predis/predis` ^3.2 - Redis client

### Development
- `phpunit/phpunit` ^11.5.3 - Testing framework
- `larastan/larastan` ^3.7 - Laravel-specific PHPStan rules
- `friendsofphp/php-cs-fixer` ^3.88 - Code style fixer
- `laravel/pint` ^1.24 - Laravel code formatter
- `barryvdh/laravel-ide-helper` ^3.6 - IDE autocompletion
- `zircote/swagger-php` ^5.4 - OpenAPI documentation generator