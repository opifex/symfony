# Infrastructure Layer

## Structure

```text
Infrastructure/
├── Doctrine/
├── Adapter/
├── HttpKernel/
├── Messenger/
├── Security/
├── Serializer/
└── ...
```

## Rules

- Implement contracts/interfaces declared in Domain/Application layers
- Map persistence models to/from domain models
- Keep technical concerns isolated
- Convert framework/transport exceptions appropriately

## Real examples (embedded)

```php
final class AccountEntityRepository implements AccountEntityRepositoryInterface
{
    public function save(Account $account): Account
    {
        // map domain -> persistence model
        // persist and flush
        // map persistence -> domain
    }
}

final class JsonLoginAuthenticator
{
    // infrastructure authentication logic
}
```

## Additional Examples

### Adapter example

```php
final class JwtAccessTokenManager implements JwtAccessTokenManagerInterface
{
    public function encode(array $claims): string
    {
        // produce JWT using infrastructure library
    }
}
```

### HTTP kernel exception mapping example

```php
final class KernelExceptionEventListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // convert internal exception to transport response
    }
}
```

### Rule reminder for Swagger

```text
Infrastructure may support docs endpoint technically,
but OA operation definitions are owned by Presentation controllers.
```
