# Application Layer

## Structure

```text
Application/
├── Command/
├── Query/
├── Contract/
├── Service/
├── MessageHandler/
└── Exception/
```

## Responsibilities

1. Transform boundary input to domain operations
2. Orchestrate domain entities/value objects
3. Use Domain/Application contracts
4. Return command/query result DTOs
5. Throw application-level exceptions where needed

## Best Practices

### ✅ DO
- Keep handlers focused and explicit
- Use Domain value objects
- Depend on interfaces/contracts
- Keep result structures simple

### ❌ DON'T
- Access database directly from handler logic
- Put presentation concerns in Application handlers
- Couple handlers to infrastructure concrete classes

## Additional Examples

### Command handler example

```php
#[AsMessageHandler]
final class BlockAccountByIdCommandHandler
{
    public function __invoke(BlockAccountByIdCommand $command): BlockAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);
        $account = $this->accountRepository->findOneById($accountId);

        $account = $this->accountStateMachine->block($account);
        $account = $this->accountRepository->save($account);

        return BlockAccountByIdCommandResult::success($account);
    }
}
```

### Query handler example

```php
#[AsMessageHandler]
final class GetAccountsByCriteriaQueryHandler
{
    public function __invoke(GetAccountsByCriteriaQuery $query): GetAccountsByCriteriaQueryResult
    {
        $searchResult = $this->accountRepository->findByCriteria(
            accountEmail: $query->email,
            accountStatus: $query->status,
            pageNumber: $query->page,
            pageSize: $query->limit,
        );

        return GetAccountsByCriteriaQueryResult::fromSearchResult($searchResult);
    }
}
```

### Swagger relation example

```text
Application handlers do not contain OA attributes.
Swagger/OpenAPI annotations are declared in Presentation controllers.
```
