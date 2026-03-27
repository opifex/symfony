# Domain Layer

## Structure

```text
Domain/
├── Account/
├── Foundation/
├── Healthcheck/
├── Localization/
└── Payment/
```

## Core Rules

- Domain is framework-independent
- Value Objects for business concepts
- Entities encapsulate behavior and invariants
- Domain exceptions/events model domain state and actions

## Real examples (embedded)

```php
final class Account
{
    public static function create(
        AccountIdentifier $id,
        EmailAddress $email,
        PasswordHash $password,
    ): self {
        // invariant checks and state creation
    }
}

final class EmailAddress
{
    public static function fromString(string $value): self
    {
        // validate format and normalize
    }
}
```

## Additional Examples

### Domain exception example

```php
final class AccountNotFoundException extends DomainException
{
    public static function create(): self
    {
        return new self('Account not found.');
    }
}
```

### Domain event example

```php
final class AccountRegisteredEvent
{
    public static function create(Account $account): self
    {
        // event payload from domain entity
    }
}
```

### Rule reminder for Swagger

```text
Domain layer never contains OA attributes and never knows HTTP/OpenAPI concerns.
```
