[← Getting Started](getting-started.md) · [Back to README](../README.md) · [Development →](development.md)

# Architecture

MedArea follows **Domain-Driven Design (DDD)** with Clean Architecture dependency rules. For detailed guidelines and code examples, see [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md).

## Layer Overview

```
src/
├── Domain/          # Eloquent models, enums, factories, DTOs, repository contracts
├── Application/     # Services, use-case DTOs — orchestrates Domain
├── Infrastructure/  # Repository implementations, jobs, notifications
├── Presentation/    # Controllers, FormRequests, API Resources, routes
└── Shared/          # Cross-cutting utilities used by all layers
```

Each domain entity (User, Analys, File, AI/Recognise) has its own subdirectory within each layer.

### DTO Placement

DTOs are split by who consumes them:

| Location | Contains | Examples |
|----------|----------|---------|
| `Domain/*/DTO/` | DTOs used in repository contract signatures | `UserDTO`, `FileDTO`, `UserAnalysDTO`, `FilterFileDTO` |
| `Application/*/DTO/` | DTOs used only within Application use cases | `TokenResponse`, `CreateFilesRequestDTO`, `RecogniseAsyncResponse` |
| `Shared/DTO/` | Base classes | `BaseDTO`, `FilterBaseDTO` |

## Dependency Rules

```
Presentation → Application → Domain
Infrastructure → Domain (implements Domain repository contracts)
Shared → (any layer)
```

| Allowed | Forbidden |
|---------|-----------|
| `Presentation` calls `Application` service contracts | `Domain` importing `Application` or `Infrastructure` |
| `Application` uses `Domain` models, DTOs, and repository contracts | `Application` importing `Infrastructure` concrete classes |
| `Infrastructure` implements `Domain/*/Repositories/` contracts | Controllers containing business logic |
| Any layer uses `Shared` | Bypassing repository contracts with raw Eloquent |

## Namespace Roots (PSR-4)

| Namespace | Path |
|-----------|------|
| `Domain\` | `src/Domain/` |
| `Application\` | `src/Application/` |
| `Infrastructure\` | `src/Infrastructure/` |
| `Presentation\` | `src/Presentation/` |
| `Shared\` | `src/Shared/` |

> Never use `App\` for business code. Models live in `src/Domain/`, not `app/Models/`.

## Key Conventions

- **Contracts first** — every service has an interface in `Application/*/Services/Contracts/`; repository contracts live in `Domain/*/Repositories/`
- **Bind in providers** — service bindings in `ApplicationServiceProvider`, repository bindings in `InfrastructureServiceProvider`
- **DTOs at boundaries** — use `spatie/laravel-data` objects, never raw arrays between layers; domain-boundary DTOs go in `Domain/*/DTO/`
- **Async by default** — OCR/AI operations go through `Infrastructure/Jobs/`, never synchronous in a request
- **Fat services, thin controllers** — controllers validate (FormRequest) → call service → return Resource

## Bounded Domains

| Domain | Entities |
|--------|----------|
| User | Authentication, registration, JWT |
| Analys | Medical analysis records |
| File | File uploads, S3 storage |
| AI/Recognise | OCR recognition requests and results |

## See Also

- [Development](development.md) — Testing and architecture enforcement
- [Configuration](configuration.md) — Environment variables
- [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md) — Full architecture doc with code examples
