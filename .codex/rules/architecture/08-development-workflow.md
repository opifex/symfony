# Development Workflow

## Pre-Development Research

- Search similar handlers/contracts/controllers with `rg`
- Review these architecture rules before coding
- Verify dependency direction before coding
- Check existing OA attributes for similar endpoints before adding/changing routes

## Common Development Commands

```bash
# Start services
docker-compose --env-file .env.local up -d

# In application container
docker compose exec application composer auto-analyze
docker compose exec application composer auto-quality
docker compose exec application composer fix-style
docker compose exec application bin/phpunit
docker compose exec application php bin/console doctrine:migrations:migrate --no-interaction
```

## Swagger-focused workflow examples

```text
1) Add or change endpoint method/signature
2) Update OA operation attributes on the same endpoint
3) Ensure request payload docs match command/query fields
4) Ensure response codes match actual returned statuses
5) Smoke-check docs page in running app
```

## Feature Workflow

1. Create feature branch
2. Implement by layer
3. Write tests using endpoint/handler matrix (success + negative + auth + validation)
4. Run quality checks
5. Verify endpoint documentation completeness
6. Commit/push and open PR

## Test-First Micro Workflow

```text
1) Define expected status codes from OA attributes
2) Add/adjust functional tests for those statuses
3) Add/adjust unit tests for changed business branches
4) Implement code changes
5) Re-run tests and align OA docs if behavior changed
```
