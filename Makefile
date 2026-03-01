.PHONY: help test test-unit test-feature test-arch test-file cs-fix phpstan build dev clean

# Default target
help:
	@echo "MedArea - Available commands:"
	@echo ""
	@echo "  make test           - Run all tests"
	@echo "  make test-unit      - Run unit tests"
	@echo "  make test-feature   - Run feature tests"
	@echo "  make test-arch      - Run architecture tests"
	@echo "  make test-file      - Run specific test file (FILE=tests/Feature/MyTest.php)"
	@echo "  make cs-fix         - Fix code style with PHP CS Fixer"
	@echo "  make phpstan        - Run PHPStan static analysis"
	@echo "  make check          - Run all checks (tests + cs-fix + phpstan)"
	@echo "  make dev            - Start development environment"
	@echo "  make build          - Build Docker containers"
	@echo "  make clean          - Clean Docker containers and volumes"
	@echo ""

# Run all tests
test:
	docker-compose exec php composer test

# Run unit tests only
test-unit:
	docker-compose exec php php artisan test tests/Unit

# Run feature tests only
test-feature:
	docker-compose exec php php artisan test tests/Feature

# Run architecture tests only
test-arch:
	docker-compose exec php php artisan test tests/Architecture

# Run specific test file
# Usage: make test-file FILE=tests/Feature/MyTest.php
test-file:
	docker-compose exec php php artisan test $(FILE)

# Fix code style
cs-fix:
	docker-compose exec php composer cs-fixer:fix

# Run static analysis
phpstan:
	docker-compose exec php php -d error_reporting=E_ALL\&~E_DEPRECATED vendor/bin/phpstan analyse --memory-limit=2G --configuration=phpstan.neon

# Run all checks before commit
check: test cs-fix phpstan
	@echo ""
	@echo "✅ All checks passed!"

# Start development environment
dev:
	docker-compose up -d
	@echo ""
	@echo "✅ Development environment started!"
	@echo "   Web: http://localhost"
	@echo "   Run migrations: docker-compose exec php php artisan migrate"

# Build Docker containers
build:
	docker-compose build

# Stop and clean Docker containers and volumes
clean:
	docker-compose down -v
	@echo ""
	@echo "✅ Docker containers and volumes cleaned!"
