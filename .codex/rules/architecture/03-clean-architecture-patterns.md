# Clean Architecture Patterns

## Command/Query Pattern (closest equivalent)

```php
final class CreateNewAccountCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}

#[AsMessageHandler]
final class CreateNewAccountCommandHandler
{
    public function __invoke(CreateNewAccountCommand $command): CreateNewAccountCommandResult
    {
        // orchestrate domain and return result
    }
}
```

```php
final class GetHealthStatusQuery
{
}

#[AsMessageHandler]
final class GetHealthStatusQueryHandler
{
    public function __invoke(GetHealthStatusQuery $query): GetHealthStatusQueryResult
    {
        // read model and return status
    }
}
```

## Data Access Pattern (closest equivalent)

```php
interface AccountEntityRepositoryInterface
{
    public function findOneById(AccountIdentifier $id): Account;
    public function save(Account $account): Account;
}

final class AccountEntityRepository implements AccountEntityRepositoryInterface
{
    public function findOneById(AccountIdentifier $id): Account
    {
        // infrastructure implementation
    }
}
```

## Value Object Pattern

Use domain value objects (e.g. `EmailAddress`, `AccountIdentifier`, `DateTimeUtc`) instead of loose scalar strings where business constraints apply.

```php
$email = EmailAddress::fromString($command->email);
$identifier = AccountIdentifier::fromString($uuidGenerator->generate());
```

## Presentation Layer Rules

Controllers/commands:
1. Validate input at boundary
2. Transform to command/query
3. Dispatch via message bus
4. Transform output to HTTP/CLI response

## Dependency Injection

```php
$services = $container->services()->defaults()->autowire()->autoconfigure();
$services->load(namespace: 'App\\', resource: '<project_sources>');
```

## Swagger Pattern Example

```php
#[OA\Patch(summary: 'Update account by identifier', security: [['Bearer' => []]])]
#[OA\Tag(name: 'Account')]
#[OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object'))]
#[OA\PathParameter(name: 'id', required: true)]
#[OA\Response(response: 204, description: 'No Content')]
#[OA\Response(response: 400, description: 'Bad Request')]
#[OA\Response(response: 404, description: 'Not Found')]
```
