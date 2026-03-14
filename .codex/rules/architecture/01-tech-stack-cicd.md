# Tech Stack & CI/CD Configuration

## Tech Stack

- **PHP 8.5** with strict types enabled
- **Symfony 8.0** framework
- **Doctrine ORM 3.6**
- **PHPCS**
- **PHPStan level max**
- **PHPUnit 12.5**
- **NelmioApiDocBundle/OpenAPI attributes** for API documentation

## Code Quality Tools

- PHPCS check: `composer auto-analyze`
- PHPCS fix: `composer fix-style`
- PHPStan: `composer auto-analyze`
- Full quality + tests: `composer auto-quality`

## CI/CD

CI runs:
- `composer auto-analyze`
- `composer auto-quality`

Example CI step:

```yaml
- name: Run static code analysis
  run: composer auto-analyze

- name: Run tests and quality checks
  run: composer auto-quality
```

## Additional Examples

### Local quality flow

```bash
docker compose exec application composer auto-analyze
docker compose exec application composer auto-quality
```

### Swagger exposure flow

```bash
# after starting app, open docs endpoint in browser
# /docs
```

### Minimal OpenAPI operation example

```php
#[OA\Get(summary: 'Get resource')]
#[OA\Tag(name: 'Resource')]
#[OA\Response(response: 200, description: 'OK')]
```
