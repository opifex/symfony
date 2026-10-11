---
paths:
  - "src/Infrastructure/**"
---

# Infrastructure Layer

Technical implementations of Domain/Application contracts. May import from Domain and Application.

## Doctrine Repository Pattern

```
Infrastructure/Doctrine/Repository/{Module}/
├── {Module}EntityRepository.php   # implements Domain repository interface
└── {Module}EntityMapper.php       # maps ORM Entity ↔ Domain Aggregate
```

- `final readonly class` implementing domain `*RepositoryInterface`
- All interface methods marked `#[Override]`
- Uses QueryBuilder for queries, detaches entities after retrieval
- Throws domain exceptions (not Doctrine exceptions)
- Soft deletes via `deletedAt` column

### Entity Mapper

- `final readonly class` with static-only methods
- `static map(Entity): DomainAggregate` — hydrates all value objects from primitives
- `static mapAll(Entity ...): array` — batch mapping with spread + array_map

## Doctrine Entity Mapping

- Located in `Infrastructure/Doctrine/Mapping/`
- PHP 8 attribute mapping (`#[ORM\Entity]`, `#[ORM\Column]`)
- Public properties with defaults (not readonly — Doctrine needs mutability)
- Naming strategy: `underscore_number_aware` (camelCase → snake_case)
- Separate from Domain aggregates — never expose ORM entities outside Infrastructure

## Messenger Bus Wrappers

- `CommandMessageBus` / `QueryMessageBus`: enforce exactly one handler per message
- `EventMessageBus`: allows zero or many handlers (`allow_no_handlers`)
- All use `#[Lazy]` for deferred initialization, `#[Autowire(service: 'bus.name')]`

## Middleware

- `CorrelationIdMiddleware`: propagates correlation IDs across async boundaries
- `MessageValidationMiddleware`: validates message DTOs before handling, throws `ValidationFailedException`

## Adapter Pattern

Third-party integrations in `Infrastructure/Adapter/{Library}/`:
- Implement domain/application contracts
- Wrap library exceptions in domain exceptions
- Examples: `Lcobucci/` (JWT), `PayPal/` (webhooks), `Kennethreitz/` (httpbin)

## Anti-patterns

- Business logic in repositories or adapters
- Exposing ORM entities outside this layer
- Domain aggregate mutation inside repositories
- Framework-specific exceptions leaking to Application/Domain
