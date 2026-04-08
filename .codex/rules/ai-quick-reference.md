# AI Quick Reference - PHP Clean Architecture Backend

This file is a quick index only.

**Single source of truth:** detailed rules live in `architecture/01..09` and must not be duplicated here.

## CRITICAL - NEVER DO (Red Lines)

### 1. Business Logic in Presentation Layer
No business logic in controllers/CLI/message entrypoints.

Details:
- [07-presentation-layer](architecture/07-presentation-layer.md)
- [02-clean-architecture-principles](architecture/02-clean-architecture-principles.md)

### 2. Framework Coupling in Domain/Application
No Symfony/Doctrine coupling in Domain core and Application business orchestration.

Details:
- [02-clean-architecture-principles](architecture/02-clean-architecture-principles.md)
- [04-application-layer](architecture/04-application-layer.md)
- [05-domain-layer](architecture/05-domain-layer.md)

### 3. Direct Database Access in Application Handlers
No EntityManager/QueryBuilder/SQL directly in command/query handlers.

Details:
- [04-application-layer](architecture/04-application-layer.md)
- [06-infrastructure-layer](architecture/06-infrastructure-layer.md)

### 4. Scalar Instead of Value Objects for Domain Concepts
Use Value Objects for meaningful domain data.

Details:
- [05-domain-layer](architecture/05-domain-layer.md)
- [03-clean-architecture-patterns](architecture/03-clean-architecture-patterns.md)

### 5. Implementing Without Research
Always inspect existing patterns first.

Details:
- [08-development-workflow](architecture/08-development-workflow.md)

### 6. Skipping Code Quality Checks
Do not finalize changes without quality gates.

Details:
- [01-tech-stack-cicd](architecture/01-tech-stack-cicd.md)
- [08-development-workflow](architecture/08-development-workflow.md)

### 7. Skipping Mandatory Test Matrix
Do not ship behavior changes without full test matrix coverage.

Details:
- [09-testing](architecture/09-testing.md)

### 8. Missing OpenAPI Contract on HTTP Endpoints
Every HTTP endpoint in Presentation layer must have OpenAPI attributes.

Details:
- [07-presentation-layer](architecture/07-presentation-layer.md)
- [02-clean-architecture-principles](architecture/02-clean-architecture-principles.md)
- [09-testing](architecture/09-testing.md)

## ALWAYS DO FIRST (Mandatory Research)

1. Read relevant sections in `architecture/01..09` before coding.
2. Reuse existing patterns from current codebase.
3. Confirm dependency direction (Presentation -> Application -> Domain).
4. For endpoint changes, update OpenAPI and matching tests in the same change.

## Canonical Rules Map (01..09)

- [01-tech-stack-cicd](architecture/01-tech-stack-cicd.md)
- [02-clean-architecture-principles](architecture/02-clean-architecture-principles.md)
- [03-clean-architecture-patterns](architecture/03-clean-architecture-patterns.md)
- [04-application-layer](architecture/04-application-layer.md)
- [05-domain-layer](architecture/05-domain-layer.md)
- [06-infrastructure-layer](architecture/06-infrastructure-layer.md)
- [07-presentation-layer](architecture/07-presentation-layer.md)
- [08-development-workflow](architecture/08-development-workflow.md)
- [09-testing](architecture/09-testing.md)
