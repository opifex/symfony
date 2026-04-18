---
paths:
  - "src/Application/**"
---

# Application Layer

Use case orchestration. Depends on Domain only.

## Command/Query Structure

Every command and query follows a tripartite folder pattern:

```
Application/Command/{UseCaseName}/
├── {UseCaseName}Command.php         # final readonly DTO
├── {UseCaseName}CommandHandler.php   # #[AsMessageHandler], __invoke()
└── {UseCaseName}CommandResult.php    # final readonly, implements JsonSerializable
```

### Message DTO (Command/Query)

- `final readonly class` with constructor-promoted properties
- Validation via Symfony attributes: `#[Assert\Email]`, `#[Assert\NotBlank]`, `#[Assert\PasswordStrength]`
- Default values for all properties (enables partial construction)
- No methods beyond `__construct`

### Handler

- `final readonly class` with `#[AsMessageHandler]` attribute
- Single `__invoke(CommandType $command): ResultType` method
- Dependencies injected via constructor (domain contracts only)
- Uses pipe operator for transformation chains:
  ```php
  $account = Account::create(...) |> $this->stateMachine->register(...) |> $this->repo->save(...);
  ```

### Result

- `final readonly class implements JsonSerializable`
- Private constructor with `mixed $payload`
- Static factory: `success(DomainEntity $entity): self` — transforms to JSON-ready array
- Commands return minimal data (id); queries return full projection

## Contract Interfaces

Located in `Application/Contract/` — define capabilities the Application layer needs:
- `CommandMessageBusInterface`, `QueryMessageBusInterface`, `EventMessageBusInterface`
- `JwtAccessTokenIssuerInterface`, `AuthorizationTokenStorageInterface`
- `UuidIdentityGeneratorInterface`, `AuthenticationRateLimiterInterface`

## Anti-patterns

- Importing from `App\Infrastructure` or `App\Presentation`
- Direct database queries or Doctrine usage
- HTTP/framework concerns (Request, Response objects)
- Business rules that belong in Domain entities
- Handlers with more than one public method
