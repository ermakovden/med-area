[← Architecture](architecture.md) · [Back to README](../README.md) · [Configuration →](configuration.md)

# Development

## Coding Standards

- **Style Guide:** PER-CS (PHP-FIG Extended Coding Style)
- **PHP Version:** 8.5+ features allowed (enums, readonly, typed properties, fibers)
- **Tooling:** PHP CS Fixer with `@PER-CS` and `@PHP85Migration` rules

```bash
make cs-fix           # Fix code style
composer cs-fixer:fix # Alternative
```

Run before every commit. CI will fail on style violations.

## Static Analysis

- **Tool:** PHPStan with Larastan
- **Level:** 8 (strict)
- **Scope:** `src/` directory

```bash
make phpstan
composer phpstan
```

## Testing

| Suite | What it tests |
|-------|---------------|
| `Architecture` | Layer dependency rules (no cross-layer violations) |
| `Unit` | Isolated component logic |
| `Feature` | HTTP endpoints via full request cycle |

```bash
make test                                         # All suites
make test-unit                                    # Unit only
make test-feature                                 # Feature only
make test-arch                                    # Architecture only
make test-file FILE=tests/Feature/MyTest.php      # Specific file
```

**Database:** Tests use SQLite in-memory (`DB_CONNECTION=sqlite` in `phpunit.xml`).

**Before committing:**
```bash
make check   # Runs: tests + cs-fix + phpstan
```

## Continuous Integration

- **Platform:** GitHub Actions
- **Trigger:** Every push and pull request
- **Workflow:** `.github/workflows/tests.yml`
- **PHP Version:** 8.5
- **Checks:** PHPUnit, PHPStan, PHP CS Fixer

## API Documentation

- **Format:** OpenAPI 3.0 (YAML)
- **File:** `openapi.yaml`
- **Regenerate:** `composer doc` (uses `zircote/swagger-php` annotations)

## See Also

- [Architecture](architecture.md) — Layer rules and conventions
- [Getting Started](getting-started.md) — Running the project locally
- [Configuration](configuration.md) — Environment variables
