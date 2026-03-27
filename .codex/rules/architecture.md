# Architecture Overview

This document provides an overview of the Clean Architecture implementation for PHP backend services.

## Description

This project follows Clean Architecture principles with a pragmatic approach. It's based on Symfony 8 framework and uses Domain-Driven Design (DDD) for organizing domain structure.

**Project**: Symfony example application
**Namespace**: `App\`

## Architecture Layers

The application is organized into four main layers:

1. **Domain Layer** - Core business logic, entities, value objects, enums, and events
2. **Application Layer** - Commands/Queries, handlers, contracts, and orchestration logic
3. **Infrastructure Layer** - Technical implementations (Doctrine, adapters, messenger, security, serializer)
4. **Presentation Layer** - HTTP controllers, CLI commands, scheduler tasks, request/response handling

## Key Principles

- **Dependency Rule**: Dependencies point inward. Outer layers depend on inner layers, never the reverse.
- **Independence**: Domain and Application layers are independent of frameworks and external dependencies where business logic is concerned.
- **Testability**: Business logic can be tested without frameworks or databases.
- **Flexibility**: Frameworks and technologies can be swapped without affecting business logic.

## Rule Quality Standards

These rules must follow quality principles:
- Keep instructions concise and action-oriented
- Prefer deterministic checklists for fragile operations
- Use concrete code snippets over abstract prose
- Keep freedom level explicit:
  - Low freedom: mandatory contracts and forbidden patterns
  - Medium freedom: preferred patterns and templates
  - High freedom: architecture-level guidance
- Every endpoint-related rule must include OpenAPI contract expectations
- Every behavior-related rule must include test expectations

## Additional Examples

### Layer Dependency Examples

```text
Correct:
Presentation -> Application
Application -> Domain
Infrastructure -> Domain/Application contracts

Incorrect:
Domain -> Infrastructure
Domain -> Presentation
Application -> Infrastructure concrete class
```

### Request Flow Example

```text
HTTP Request
-> Presentation Controller
-> Application Command/Query Handler
-> Domain Entity/ValueObject logic
-> Infrastructure repository/adapter
-> Application Result
-> Presentation JSON Response
```

### Swagger Documentation Example

```php
#[OA\Get(summary: 'Get health status')]
#[OA\Tag(name: 'Health')]
#[OA\Response(response: 200, description: 'OK')]
public function __invoke(GetHealthStatusQuery $query): Response
{
    $result = $this->queryMessageBus->ask($query);

    return $this->json($result, status: 200);
}
```

## Documentation Structure

- 01-tech-stack-cicd
- 02-clean-architecture-principles
- 03-clean-architecture-patterns
- 04-application-layer
- 05-domain-layer
- 06-infrastructure-layer
- 07-presentation-layer
- 08-development-workflow
- 09-testing
