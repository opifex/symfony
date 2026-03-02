# Testing

## Test Structure

```text
tests/
├── Functional/
├── Unit/
└── Support/
```

## Testing Levels

- Unit: domain/infrastructure/application units in isolation
- Functional: HTTP/CLI end-to-end through kernel
- Integration concerns are covered where functional/unit composition requires it

## Quality Bar (Mandatory)

For every behavior change, tests must cover:
1. Success scenario
2. Permission/auth scenario (when endpoint is protected)
3. Validation/bad-input scenario
4. Domain/business failure scenario (not found/conflict/invalid state)
5. OpenAPI contract alignment for status codes and required fields

If one of these cases is not applicable, state explicitly in test naming/comments.

## Patterns in this repo

- Functional tests use `WebTestCase`
- Reusable fixtures/helpers/schema assertions are kept in `Support`
- Test runtime uses project PHPUnit bootstrap and config

## Functional Test Patterns (HTTP)

### Pattern 1: Happy path + schema assertion

```php
final class CreateNewAccountWebTest extends WebTestCase
{
    public function testEnsureAdminCanCreateAccount(): void
    {
        // arrange fixtures
        // send request
        // assert status and schema
    }
}
```

### Pattern 2: Auth + permission matrix

```php
public function testEnsureAdminCanGetAccountsByCriteria(): void
{
    $this->loadFixtures([AccountActivatedAdminFixture::class]);
    $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
    $this->sendGetRequest(url: '/api/account', params: ['status' => 'activated']);
    $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    $this->assertResponseSchema(schema: 'GetAccountsByCriteriaSchema.json');
}

public function testTryToGetAccountsByCriteriaWithoutPermission(): void
{
    $this->loadFixtures([AccountActivatedJamesFixture::class]);
    $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
    $this->sendGetRequest(url: '/api/account', params: ['status' => 'activated']);
    $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
    $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
}
```

### Pattern 3: Invalid payload / extra fields

```php
public function testTryToSigninWithExtraAttributes(): void
{
    $this->loadFixtures([AccountActivatedAdminFixture::class]);
    $this->sendPostRequest(url: '/api/auth/signin', params: [
        'email' => 'admin@example.com',
        'password' => 'password4#account',
        'extra' => 'value',
    ]);
    $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
    $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
}
```

## Unit Test Patterns

### Pattern 1: Value Object validation + normalization

```php
final class EmailAddressTest extends TestCase
{
    public function testInvalidEmailThrowsDomainException(): void
    {
        $this->expectException(DomainException::class);
        EmailAddress::fromString('not-an-email');
    }

    #[DataProvider(methodName: 'emailAddressProvider')]
    public function testNormalizeWithDifferentTypes(mixed $value, mixed $expected): void
    {
        $emailAddress = EmailAddress::fromString($value);
        $this->assertSame($expected, $emailAddress->toString());
    }
}
```

### Pattern 2: Handler exception paths with mocked contracts

```php
public function testInvokeThrowsThrottlingException(): void
{
    $handler = new SigninIntoAccountCommandHandler(
        accountEntityRepository: $this->accountEntityRepository,
        authenticationRateLimiter: $this->authenticationRateLimiter,
        authorizationTokenStorage: $this->authorizationTokenStorage,
        jwtAccessTokenManager: $this->jwtAccessTokenManager,
    );

    $this->authorizationTokenStorage
        ->expects($this->once())
        ->method('getUserIdentifier')
        ->willThrowException(AuthorizationRequiredException::create());

    $this->authenticationRateLimiter
        ->expects($this->once())
        ->method('isAccepted')
        ->willReturn(false);

    $this->expectException(AuthorizationThrottlingException::class);

    $handler(new SigninIntoAccountCommand());
}
```

## CLI/Console Test Pattern

```php
public function testEnsureConsoleCommandExecutesSuccessfully(): void
{
    $commandTester = new CommandTester($this->command);
    $commandTester->execute(['--delay' => 0]);
    $commandTester->assertCommandIsSuccessful();
    $this->assertStringContainsString('Success', $commandTester->getDisplay());
}
```

## Swagger-related test expectations

```text
For documented endpoints, keep behavior aligned with OA attributes:
- documented success code must match actual response code
- documented required fields must match request validation
- documented auth requirement must match security behavior
```

## OpenAPI verification scenarios

```text
When endpoint is added/changed, verify:
1) Success path returns status documented in OA Response
2) Validation error path returns documented 400-style response
3) Unauthorized/forbidden access returns documented 401/403 responses
4) Missing resource returns documented 404 response (if applicable)
5) Query/path params behave as documented in OA parameter attributes
```

```php
final class SigninIntoAccountWebTest extends WebTestCase
{
    public function testSigninReturnsDocumentedStatusCode(): void
    {
        // send valid signin payload
        // assert HTTP 200, as documented by OA\Response(response: 200)
    }

    public function testSigninUnauthorizedMatchesDocumentation(): void
    {
        // send invalid credentials
        // assert HTTP 401, as documented by OA\Response(response: 401)
    }
}
```

## Endpoint Test Matrix Template (Use For New/Changed Endpoints)

```text
[ ] success response code and schema
[ ] unauthorized (401) when no token and endpoint is protected
[ ] forbidden (403) when role is insufficient
[ ] validation error (400/422) for invalid payload or extra attributes
[ ] not found (404) when resource does not exist (if applicable)
[ ] conflict (409) for duplicate/invalid state (if applicable)
[ ] OpenAPI attributes include all actual response codes
```
