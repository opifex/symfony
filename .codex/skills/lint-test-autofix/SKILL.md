---
name: lint-test-autofix
description: Run project linters and automated tests inside Docker Compose containers, fix discovered issues in code, and repeat until checks pass or a hard blocker is reached. Use when the user asks to run lint/tests, fix CI failures, or make Composer quality checks green in a containerized Symfony/PHP project.
---

# Lint Test Autofix

## Overview

Run a strict quality loop: execute linter and test commands in the `application` container, fix failures, and re-run checks to confirm resolution. Keep edits minimal, deterministic, and scoped to root causes.

## Inputs to Collect

Identify or confirm these values before running the loop:

- Docker Compose service name (default: `application`)
- Lint command via Composer
- Test command via Composer or PHPUnit
- Optional fixer command via Composer
- Maximum iterations (default: 3)

For Symfony/PHP projects with Composer scripts, prefer:

- Lint: `docker compose exec -T application composer auto-analyze`
- Fixer: `docker compose exec -T application composer fix-style`
- Tests: `docker compose exec -T application bin/phpunit --no-progress`
- Full quality gate (optional): `docker compose exec -T application composer auto-quality`

## Workflow

1. Baseline
If required services are not running, start them, then run linter and tests without editing first to capture current failures.

2. Triage
Classify failures:
- style/formatting
- static analysis/type issues
- functional test/runtime failures
- environment failures (missing service, DB, credentials)

3. Fix
Apply the smallest safe change for each actionable failure:
- Run formatter/fixer for style violations before manual edits.
- For static analysis, prefer type-safe and explicit fixes over suppression.
- For tests, fix production code first; adjust tests only when expectations are outdated.

4. Verify
Re-run the failing command first, then run the full lint+test set.

5. Iterate
Repeat triage -> fix -> verify until green or blocked by external dependency. Stop after max iterations and report blockers with exact failing command/output summary.

## Guardrails

- Never edit generated/vendor files unless user explicitly requests it.
- Never hide failures with blanket ignores unless user approves.
- Preserve existing project conventions and tool configs.
- Keep commits/patches focused; avoid refactors unrelated to failing checks.
- If checks need unavailable infra (DB, queue, external API), report it as blocker and provide the exact missing dependency.

## Docker Compose Execution Pattern

Use this sequence by default:

```bash
# Optional: run only if required services are not running
docker compose up -d application postgres redis rabbitmq mailcatcher
docker compose exec -T application composer fix-style
docker compose exec -T application composer auto-analyze
docker compose exec -T application bin/phpunit --no-progress
```

If needed, run the project aggregate gate:

```bash
docker compose exec -T application composer auto-quality
```
