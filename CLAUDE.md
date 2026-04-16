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

| Service | Port | Purpose |
|---------|------|---------|
| application | 8030 | Symfony app (Nginx + PHP-FPM) |
| messenger | — | AMQP message consumer (3 queues) |
| migration | — | Runs migrations on startup, then exits |
| postgres | 5432 | PostgreSQL 18.3 |
| redis | 6379 | Cache & session store |
| rabbitmq | 15672 | Message broker (management UI) |
| mailcatcher | 1088 | Email testing UI |

## Architecture

```
src/
├── Domain/           # Business models, value objects, interfaces (no framework deps)
├── Application/      # Use cases: commands, queries, contracts, services
├── Infrastructure/   # Framework integrations, adapters, Doctrine, messenger
└── Presentation/     # Controllers, console commands, scheduler, resources
```

### Modules

| Module | Location | Purpose |
|--------|----------|---------|
| **Account** | `Domain/Account/` | Users, roles, registration, state machine |
| **Payment** | `Domain/Payment/` | Payment processing (PayPal webhooks) |
| **Foundation** | `Domain/Foundation/` | Shared value objects (EmailAddress, PasswordHash, DateTimeUtc) |
| **Healthcheck** | `Domain/Healthcheck/` | System health probes |
| **Localization** | `Domain/Localization/` | Multi-language support (en-US, uk-UA) |

### Layer Dependency Rules

Enforced by custom PHPCS sniff (`config/markup/Standards/Sniffs/Classes/ClassStructureSniff.php`) at lint time:

- **Domain** → no imports from Application/Infrastructure/Presentation
- **Application** → may import from Domain only
- **Infrastructure** → may import from Domain and Application
- **Presentation** → may import from Domain and Application

### Three Message Buses

Configured in `config/packages/messenger.yaml` with dedicated middleware each:

| Bus | Middleware | Notes |
|-----|-----------|-------|
| `command.bus` | correlation ID, validation, doctrine transaction | Exactly one handler per message |
| `query.bus` | correlation ID, validation, doctrine transaction | Exactly one handler per message |
| `event.bus` | correlation ID, doctrine transaction | Zero or many handlers allowed |

### CQRS Pattern

Each command/query lives in its own folder with three files:

```
Application/Command/CreateNewAccount/
├── CreateNewAccountCommand.php        # Message DTO with validation constraints
├── CreateNewAccountCommandHandler.php # __invoke() with #[AsMessageHandler]
└── CreateNewAccountCommandResult.php  # implements JsonSerializable
```

### Key Conventions

- **Immutable aggregates**: `final readonly` classes; mutation via `withX()` methods that `clone()` — marked `#[NoDiscard]`
- **Pipe operator** (`|>`): Handlers chain transformations (e.g., `Account::create(...) |> $stateMachine->register(...) |> $repo->save(...)`)
- **Value objects**: Private constructor + `fromString()` factory + validation in constructor
- **`#[SensitiveParameter]`**: On JWT tokens, password hashes, and secrets
- **Doctrine mapping**: PHP 8 attributes only (not YAML/XML), entities in `Infrastructure/Doctrine/Mapping/`
- **Separate ORM entities**: `AccountEntity` (Doctrine) ↔ `Account` (Domain) mapped via `AccountEntityMapper`
- **Contracts layer**: `Application/Contract/` and `Domain/*/Contract/` define interfaces; Infrastructure implements

## Coding Standards

- **PHPCS**: PSR-12 + custom rules in `config/markup/phpcs.xml` — strict types required, max cyclomatic complexity 15, max line length 120, bans `die`/`echo`/`var_dump`/`dd`/`eval`
- **PHPStan**: Level max with strict rules, Doctrine/Symfony/PHPUnit extensions (`config/markup/phpstan.neon`)
- **Tests**: PHPUnit 13.1, random execution order, strict mode (fails on deprecations/notices/warnings)
- All source files must declare `strict_types=1`; all concrete classes must be `final`

## Testing

- `tests/Unit/` — domain logic, value objects, handlers
- `tests/Functional/` — API endpoints via WebTestCase with `DatabaseEntityManagerTrait` + `HttpClientRequestsTrait`
  - Pattern: load fixtures → `sendAuthorizationRequest()` → `jsonRequest()` → assert status + `assertResponseSchema()`
- `tests/Support/` — shared fixtures and traits
- Fixture password for all test accounts: `password4#account`
- Coverage excludes migrations, fixtures, and Kernel

## API Documentation

Available at `http://localhost[:port]/docs` (OpenAPI/Swagger via Nelmio).

## CI

GitHub Actions on push to `main`: `composer auto-analyze` then `composer auto-quality` against PostgreSQL 18.3. Coverage uploaded to Codecov.
