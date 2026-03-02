# Presentation Layer

## Structure

```text
Presentation/
├── Controller/
├── Command/
├── Scheduler/
└── Resource/
```

## Rules

- Validate request/CLI input
- Convert boundary payloads to command/query DTOs
- Dispatch via message bus / handlers
- Return HTTP/CLI output only
- No business logic inside controllers/console commands
- OpenAPI attributes are mandatory for every HTTP controller endpoint

## OpenAPI in Presentation Layer (Mandatory)

OpenAPI is the API contract source for this project. It is defined by `OpenApi\\Attributes` directly on controller methods and used to generate Swagger docs.

### Mandatory rule

```text
No OA attributes on endpoint = endpoint is incomplete.
```

### Required attribute set per endpoint

1. Operation: `OA\\Get` / `OA\\Post` / `OA\\Patch` / `OA\\Delete`
2. Grouping: `OA\\Tag`
3. Input contract:
   - `OA\\RequestBody` for payload methods
   - `OA\\PathParameter` for each route placeholder
   - `OA\\QueryParameter` for query filters/pagination
4. Output contract:
   - Success `OA\\Response` (200/201/204)
   - Error `OA\\Response` values that are possible (400/401/403/404/409 etc.)
5. Security contract:
   - `security: [['Bearer' => []]]` for protected endpoints

### Contract mapping rules

```text
Controller behavior and OA docs must be identical:
- same required fields
- same enum/format expectations
- same status codes
- same auth requirement
```

## Real examples (embedded)

```php
#[AsController]
final class CreateNewAccountController
{
    public function __invoke(CreateNewAccountCommand $command): Response
    {
        $result = $this->commandMessageBus->dispatch($command);

        return $this->json($result, status: 201);
    }
}

#[AsCommand(name: 'app:symfony:run')]
final class SymfonyRunCommand
{
    public function __invoke(SymfonyStyle $io): int
    {
        // CLI orchestration only
        return Command::SUCCESS;
    }
}
```

## Swagger/OpenAPI Examples

### POST with request body

```php
#[OA\Post(summary: 'Signin into account')]
#[OA\Tag(name: 'Authorization')]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'email', example: 'admin@example.com'),
            new OA\Property(property: 'password', type: 'password', example: 'password4#account'),
        ],
        type: 'object',
    ),
)]
#[OA\Response(response: 200, description: 'OK')]
#[OA\Response(response: 400, description: 'Bad Request')]
#[OA\Response(response: 401, description: 'Unauthorized')]
```

### GET with query parameters

```php
#[OA\Get(summary: 'Get accounts by criteria', security: [['Bearer' => []]])]
#[OA\Tag(name: 'Account')]
#[OA\QueryParameter(name: 'email', required: false)]
#[OA\QueryParameter(name: 'status', required: false)]
#[OA\QueryParameter(name: 'page', required: false)]
#[OA\QueryParameter(name: 'limit', required: false)]
#[OA\Response(response: 200, description: 'OK')]
#[OA\Response(response: 400, description: 'Bad Request')]
#[OA\Response(response: 403, description: 'Forbidden')]
```

### Operation with path parameter

```php
#[OA\Delete(summary: 'Delete account by identifier', security: [['Bearer' => []]])]
#[OA\Tag(name: 'Account')]
#[OA\PathParameter(name: 'id', required: true)]
#[OA\Response(response: 204, description: 'No Content')]
#[OA\Response(response: 404, description: 'Not Found')]
```

### Full endpoint contract example (payload + security + responses)

```php
#[OA\Post(summary: 'Create new account', security: [['Bearer' => []]])]
#[OA\Tag(name: 'Account')]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'email', example: 'user@example.com'),
            new OA\Property(property: 'password', type: 'password', minLength: 8, maxLength: 32),
        ],
        type: 'object',
    ),
)]
#[OA\Response(response: 201, description: 'Created')]
#[OA\Response(response: 400, description: 'Bad Request')]
#[OA\Response(response: 401, description: 'Unauthorized')]
#[OA\Response(response: 403, description: 'Forbidden')]
#[OA\Response(response: 409, description: 'Conflict')]
```

### Anti-patterns (forbidden)

```text
- Route accepts query param, but no OA QueryParameter exists
- Controller returns 204, but OA documents 200
- Endpoint is protected, but OA security is missing
- Validation requires field, but OA schema does not mark it required
```
