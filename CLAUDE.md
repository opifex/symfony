# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

RESTful API built with Symfony 8.0 and PHP 8.5, using Domain-Driven Design (DDD) with Command-Query Separation (CQS). JSON-based contracts with JWT authorization. PostgreSQL database, RabbitMQ messaging, Redis caching.

## Commands

### Code Quality
```bash
composer auto-analyze         # Audit, validate, PHPCS, PHPStan
composer fix-style            # Auto-fix code style (phpcbf)
```

### Testing
```bash
./bin/phpunit                                    # Run all tests
./bin/phpunit tests/Unit/EmailAddressTest.php    # Run single test file
./bin/phpunit --filter testMethodName            # Run single test method
./bin/phpunit tests/Unit/                        # Run directory
```

Note: `composer auto-quality` runs the full QA pipeline (lint, recreate test DB, migrate, run tests) — use it for CI-like validation, not quick iterations.

### Database
```bash
composer migration-create     # Generate migration from schema changes
composer migration-up         # Apply pending migrations
composer migration-down       # Revert last migration
composer load-fixtures        # Load test fixtures
```

### Docker
```bash
docker-compose --env-file .env.local up -d      # Start all services
```

Services: application (port 8030), messenger, migration, postgres, redis, rabbitmq, mailcatcher.

## Architecture

```
src/
├── Domain/           # Business models, value objects, interfaces (no framework deps)
│   ├── Account/      # User accounts, roles, registration
│   ├── Payment/      # Payment processing (PayPal)
│   ├── Foundation/   # Core value objects (EmailAddress, PasswordHash, etc.)
│   ├── Healthcheck/  # System health probes
│   └── Localization/ # Multi-language support
├── Application/      # Use cases, orchestration
│   ├── Command/      # Write operations (CQRS commands + handlers)
│   ├── Query/        # Read operations (CQRS queries + handlers)
│   ├── Contract/     # Service interfaces (bus, etc.)
│   ├── MessageHandler/ # Async message handlers
│   └── Service/      # Application services
├── Infrastructure/   # Framework & external integrations
│   ├── Doctrine/     # ORM mappings, migrations, repositories, fixtures
│   ├── Security/     # JWT auth, rate limiting
│   ├── Messenger/    # Bus middleware (correlation ID, validation, transactions)
│   └── ...           # Adapters, serializers, HTTP client, etc.
└── Presentation/     # Client-facing layer
    ├── Controller/   # HTTP endpoints (attribute routing, JSON responses)
    ├── Command/      # Console commands
    ├── Scheduler/    # Cron tasks
    └── Resource/     # API resource DTOs
```

### Key Patterns

- **DDD layer enforcement** (enforced by custom PHPCS sniff at lint time):
  - `Domain` — zero imports from Application/Infrastructure/Presentation
  - `Application` — may import from Domain only
  - `Infrastructure` — may import from Domain and Application
  - `Presentation` — may import from Domain and Application
- **Three message buses**: command.bus, query.bus, event.bus — each with dedicated middleware (see `config/packages/messenger.yaml`)
- **CQRS**: Commands in `Application/Command/`, queries in `Application/Query/`, each with a paired handler class
  - Each command/query lives in its own folder with three files: `FooCommand.php` (message DTO), `FooCommandHandler.php` (`__invoke` with `#[AsMessageHandler]`), `FooCommandResult.php` (implements `JsonSerializable`)
  - Command/query buses enforce exactly one handler per message; event bus allows zero or many
- **Doctrine attribute mapping**: Entity mappings use PHP 8 attributes, not YAML/XML
- **Contracts layer**: `Application/Contract/` defines interfaces implemented in Infrastructure

## Coding Standards

### Conventions
- **Immutable aggregates**: Domain entities use `readonly` classes; mutation via `withX()` methods that `clone()` — marked `#[NoDiscard]` so unused returns are flagged
- **Pipe operator** (`|>`): Handlers chain transformations (e.g., `Account::create(...) |> $stateMachine->register(...) |> $repo->save(...)`)
- **Value objects**: Private constructor + `fromString()` factory method + validation in constructor
- **`#[SensitiveParameter]`**: Used on JWT tokens, password hashes, and secrets to redact from stack traces

- **PHPCS**: PSR-12 + custom rules in `config/markup/phpcs.xml`. Enforces strict types, bans `die`/`echo`/`var_dump`/`dd`/`eval`, max cyclomatic complexity 15, max line length 120
- **PHPStan**: Level max with strict rules, Doctrine/Symfony/PHPUnit extensions (`config/markup/phpstan.neon`)
- **Tests**: PHPUnit 13.1, random execution order, strict mode (fails on deprecations/notices/warnings)
- All source files must declare `strict_types=1`

## Testing

- `tests/Unit/` — domain logic, value objects
- `tests/Functional/` — API endpoint tests using WebTestCase with `DatabaseEntityManagerTrait` (fixture loading) and `HttpClientRequestsTrait` (auth + OpenAPI schema assertions)
  - Pattern: load fixtures → `sendAuthorizationRequest()` → `jsonRequest()` → assert status + `assertResponseSchema()`
- `tests/Support/` — shared fixtures and traits (`DatabaseEntityManagerTrait`, `HttpClientRequestsTrait`)
- Coverage excludes migrations, fixtures, and Kernel

## API Documentation

Available at `http://localhost[:port]/docs` (OpenAPI/Swagger via Nelmio).
