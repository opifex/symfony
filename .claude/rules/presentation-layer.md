---
paths:
  - "src/Presentation/**"
---

# Presentation Layer

Thin HTTP/CLI layer. Delegates to Application via command/query buses.

## Controller Pattern

- `final class extends AbstractController` with `#[AsController]`
- Single public method: `__invoke()` — one action per controller
- Request body auto-deserialized via `#[ValueResolver('payload')]` into Command/Query DTO
- Dispatches via `$this->commandMessageBus->dispatch()` or `$this->queryMessageBus->dispatch()`
- Returns `$this->json($result, status: Response::HTTP_CREATED)`

```php
#[Route(path: '/account', name: 'app_create_account', methods: Request::METHOD_POST)]
#[IsGranted('ROLE_ADMIN')]
public function __invoke(#[ValueResolver('payload')] CreateNewAccountCommand $command): Response
{
    return $this->json(
        data: $this->commandMessageBus->dispatch($command),
        status: Response::HTTP_CREATED,
    );
}
```

## AbstractController

- Extends Symfony's `BaseController`
- Injects `CommandMessageBusInterface` + `QueryMessageBusInterface` as protected readonly
- No other services — controllers stay thin

## Routing

- Attribute-based: `#[Route(path: '...', name: '...', methods: '...')]`
- Route names: `app_{use_case}_{entity}` (e.g., `app_create_account`)
- Security: `#[IsGranted('ROLE_ADMIN')]` or `#[IsGranted('ROLE_USER')]`

## OpenAPI Documentation

- Every controller annotated with `#[OA\Post]`, `#[OA\RequestBody]`, `#[OA\Response]`, etc.
- Schema definitions reference component schemas
- API docs served at `/docs` via Nelmio

## Anti-patterns

- Business logic in controllers — delegate to command/query handlers
- Direct repository or entity manager access
- Multiple actions per controller class
- Returning raw arrays — use Result DTOs that implement `JsonSerializable`
