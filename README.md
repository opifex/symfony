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

DEFAULT_URI=http://localhost:8030
API_GATEWAY_URI=http://localhost:8030

DATABASE_URL=postgresql://admin:password@postgres:5432/symfony?serverVersion=17&charset=utf8
HTTPBIN_URL=https://httpbin.org/
JWT_PASSPHRASE=3a8d33b54f002565767e28d24743ad51b30a061ce31b9516b98efd64612009d721ca1c68fb7143193af754c352bf4edb2796fd13b89395d983c5c337d5a44be4
LOCK_DSN=redis://redis:6379?timeout=1&read_timeout=1
MAILER_DSN=smtp://mailcatcher:1025
MESSENGER_TRANSPORT_DSN=amqp://rabbitmq:5672/%2f/messages
PAYPAL_WEBHOOK_TOKEN=32045343896bbc210ab2924776f349d5d849709fd33b7697a7dbfc947795ddf0
REDIS_DSN=redis://redis:6379?timeout=1&read_timeout=1

SYMFONY_IDE=idea://open?file=%f&line=%l&/opt/project>/local/path
```

Generate real values and set them in `.env.local`.

```
$ openssl rand -hex 64   # JWT_PASSPHRASE  (256+ bits, required for HS256)
$ openssl rand -hex 32   # PAYPAL_WEBHOOK_TOKEN
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
│  Presentation  │ ─ │  Application   │ ─ │ Infrastructure │
└────────────────┘   └────────────────┘   └────────────────┘
        │                    │                    │
┌────────────────┐   ┌────────────────┐   ┌────────────────┐
│     Client     │   │    Services    │   │   3rd-party    │
└────────────────┘   └────────────────┘   └────────────────┘
```

**Domain**: business models, exceptions, and interfaces that define behavior but lack specific implementations.

**Application**: services, events, listeners, and other components that handle core and business logic.

**Infrastructure**: adapters, repositories, and framework modules that facilitate low-level access to resources and third-party libraries.

**Presentation**: controllers, console commands, translations, and views for client interaction.

## Documentation

You can easily access API documentation via URL.

```
http://localhost[:app-port]/docs
```
