# symfony

An example application using Symfony Framework.

![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/opifex/symfony/development.yml)
![GitHub License](https://img.shields.io/github/license/opifex/symfony)
![Codecov](https://img.shields.io/codecov/c/github/opifex/symfony)

## Configuration

Create custom configuration files in the project root directory.

Create `.env.local` and set it as docker environment variables file.

```dotenv
APP_ENV=dev
APP_NAME=symfony
APP_PORT=8030
APP_SECRET=166f851291ebd0ebf805b0188f1d5e7a
APP_URL=http://localhost:8030

DATABASE_URL=postgresql://admin:password@postgres:5432/symfony?serverVersion=15&charset=utf8
HTTPBIN_URL=http://mockserver:1080/httpbin.org/
MAILER_DSN=smtp://mailcatcher:1025
MESSENGER_TRANSPORT_DSN=amqp://rabbitmq:5672/%2f/messages
MOCK_SERVER_URL=http://mockserver:1080
REDIS_DSN=redis://redis:6379?timeout=1&read_timeout=1

SYMFONY_IDE=idea://open?file=%f&line=%l&/opt/project>/local/path
```

Create `codeception.yml` with the following set of parameters.

```yaml
params:
  - .env
  - .env.local
  - .env.test
```

## Development

Run all development services or specified containers as you need.

```
$ docker-compose --env-file .env.local up -d [--no-deps] [containers]
```

Main application containers list.

```
application crontab messenger migration
```

Use the following command when you need some data in the local database.

```
$ composer load-fixtures
```

The xdebug extension is already included in the project and is activated for development environment.

All you need to do is configure the debugger in the IDE, enter the required key and set up directory mapping.

## Architecture

This **RESTful** application uses **Domain-Driven Design** (DDD) with **Command-Query Separation** (CSQ) principles and
provides **JSON-based** contracts with **JSON Web Token** (JWT) authorization.

```
┌──────────────────────────────────────────────────────────┐
│                          Domain                          │
└──────────────────────────────────────────────────────────┘
        │                    │                    │
┌────────────────┐   ┌────────────────┐   ┌────────────────┐
│  Application   │ ─ │  Presentation  │   │ Infrastructure │
└────────────────┘   └────────────────┘   └────────────────┘
        │                    │                    │
┌────────────────┐   ┌────────────────┐   ┌────────────────┐       
│    Services    │   │     Client     │   │   3rd-party    │       
└────────────────┘   └────────────────┘   └────────────────┘
```

**Domain**: entities, exceptions and interfaces that do not have a specific implementation.

**Application**: services, events, listeners and other parts of the application that perform core and business logic.

**Infrastructure**: adapters, repositories and framework modules that provide low-level access to resources and 3rd-party libraries.

**Presentation**: controllers, console commands, translations and views for interacting with the client.

## Documentation

You can easily access API documentation via URL.

```
http://localhost[:app-port]/docs
```
