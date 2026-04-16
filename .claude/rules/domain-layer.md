---
paths:
  - "src/Domain/**"
---

# Domain Layer

Pure business logic — zero framework dependencies. Enforced by PHPCS ClassStructureSniff.

## Entity / Aggregate Pattern

- `final readonly class` with public properties (immutability via readonly)
- Factory method `create()` for construction with defaults
- Mutation via `withX()` methods using `clone($this, ['field' => $value])`
- All `withX()` methods marked `#[NoDiscard]` — unused returns flagged
- Identity via typed UUID wrapper extending `AbstractUuidIdentifier`

```php
final readonly class Account {
    public static function create(AccountIdentifier $id, EmailAddress $email, ...): self
    public function withEmail(EmailAddress $email): self  // #[NoDiscard]
}
```

## Value Object Pattern

- `final readonly class`, private constructor
- Static factory: `fromString()`, `fromInterface()`, or `now()`
- Validation in factory — throws `DomainException` on invalid input
- Conversion: `toString()`, `toAtomString()`, `toArray()`
- Equality: `equals()` method comparing by value

Key VOs: `EmailAddress`, `PasswordHash` (validates bcrypt + argon2), `DateTimeUtc` (enforces UTC), `AccountIdentifier`

## Enum Pattern

- String-backed enums with `fromString()`, `toString()`, `values()` methods
- Used in validator constraints: `#[Assert\Choice(callback: [LocaleCode::class, 'values'])]`

## Repository Interfaces

- Live in `{BoundedContext}/Contract/` subdirectory
- Return domain aggregates (not ORM entities)
- Declare thrown exceptions in docblocks

## Domain Events

- `final readonly class` in `{BoundedContext}/Event/`
- Static factory `create()` accepting the aggregate
- Dispatched via `EventMessageBusInterface` from Application handlers

## Exceptions

- Extend `RuntimeException`, not `Exception`
- Static named constructor: `create(?Throwable $previous = null): self`
- HTTP mapping via `#[WithHttpStatus(statusCode: 404)]` attribute

## Anti-patterns

- Importing anything from `App\Application`, `App\Infrastructure`, or `App\Presentation`
- Using Doctrine, Symfony, or any framework class
- Setters or mutable state — always return new instance via `withX()`
- Generic exceptions — use domain-specific exception classes
