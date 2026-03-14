# Clean Architecture Principles

**Project**: Symfony example application  
**Namespace**: `App\`

## Dependency Rule

Dependencies point inward:

```text
Presentation → Application → Domain
Infrastructure → Application/Domain (via contracts/interfaces)
```

## Layer Independence

- Domain: no framework/DB/UI coupling
- Application: orchestration via commands/queries/contracts, no infrastructure details

## Project Structure

```text
Application: Command, Query, Contract, Service, Exception
Domain: Account, Foundation, Healthcheck, Localization, Payment
Infrastructure: Doctrine, Adapter, HttpKernel, Messenger, Security
Presentation: Controller, Command, Scheduler, Resource
```

## Additional Examples

### Correct dependency

```php
final class GetAccountByIdController
{
    public function __invoke(GetAccountByIdQuery $query): Response
    {
        $result = $this->queryMessageBus->ask($query);

        return $this->json($result);
    }
}
```

### Incorrect dependency

```php
final class Account
{
    public function bad(): void
    {
        // forbidden: domain depending on framework/infrastructure
        // new EntityManager(...)
    }
}
```

### Swagger ownership example

```text
Swagger/OpenAPI attributes belong to Presentation endpoints only.
Domain and Application layers must not contain OA attributes.
```

### OpenAPI responsibility boundary

```text
Presentation layer owns API contract and endpoint documentation.
Application layer owns use-case orchestration.
Domain layer owns business rules.
Infrastructure layer owns technical implementation details.
```

```text
If API contract changes (request body, params, response codes, auth),
the change MUST be applied in Presentation OA attributes in the same update.
```
