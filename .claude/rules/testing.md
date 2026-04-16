---
paths:
  - "tests/**"
---

# Testing

PHPUnit 13.1 with random execution order and strict mode (fails on deprecations/notices/warnings).

## Test Types

### Unit Tests (`tests/Unit/`)

- Extend `PHPUnit\Framework\TestCase`
- Attributes: `#[AllowDynamicProperties]`, `#[AllowMockObjectsWithoutExpectations]`
- Mocking: PHPUnit's `createMock()` — no Prophecy or Mockery
- DataProviders via `#[DataProvider('providerName')]` returning `iterable` with named yields

```php
#[DataProvider(methodName: 'emailAddressProvider')]
public function testNormalizeWithDifferentTypes(mixed $value, mixed $expected): void
{
    self::assertSame($expected, EmailAddress::fromString($value)->toString());
}

public static function emailAddressProvider(): iterable
{
    yield 'already normalized' => ['value' => 'email@example.com', 'expected' => 'email@example.com'];
}
```

### Functional Tests (`tests/Functional/`)

- Extend `WebTestCase`, use `DatabaseEntityManagerTrait` + `HttpClientRequestsTrait`
- `setUp()` calls `self::loadHttpClient()`
- Pattern: load fixtures → authenticate → request → assert status + schema

```php
public function testEnsureAdminCanCreateAccount(): void
{
    self::loadFixtures([AccountActivatedAdminFixture::class]);
    self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
    self::sendPostRequest(url: '/api/account', params: ['email' => 'new@example.com', ...]);
    self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CREATED);
    self::assertResponseSchema();  // validates against OpenAPI spec
}
```

## Test Traits (`tests/Support/`)

| Trait | Provides |
|-------|----------|
| `DatabaseEntityManagerTrait` | `loadFixtures()`, `getDatabaseEntity()` — purges DB between tests |
| `HttpClientRequestsTrait` | `sendGetRequest()`, `sendPostRequest()`, `sendAuthorizationRequest()`, `assertResponseSchema()` |
| `HttpMockClientTrait` | `loadMockResponses()` for unit-testing HTTP clients |

## Fixtures

- Located in `tests/Support/Fixture/` (test) and `src/Infrastructure/Doctrine/Fixture/` (app)
- Use Doctrine DataFixtures + Faker
- All test accounts use password: `password4#account`
- Named references: `$this->addReference(name: 'account:activated:james', object: $entity)`

## Naming Conventions

- Success tests: `testEnsure[Actor]Can[Action]` (e.g., `testEnsureAdminCanSignin`)
- Failure tests: `testTryTo[Action][FailureCondition]` (e.g., `testTryToSigninWithNonactivatedUser`)
- All test classes: `final class` with `#[Override]` on `setUp()`

## Anti-patterns

- Test interdependencies — each test loads its own fixtures
- Asserting response body manually — use `assertResponseSchema()` for contract validation
- Skipping `assertResponseSchema()` — all endpoints must validate against OpenAPI spec
