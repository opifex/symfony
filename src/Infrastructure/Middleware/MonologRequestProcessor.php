<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
final class MonologRequestProcessor
{
    private ?string $uuid = null;

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $this->uuid ??= $this->extractIdentifierFromRequest();

        $record->extra['uuid'] = $this->uuid ?? '';

        return $record;
    }

    private function extractIdentifierFromRequest(): ?string
    {
        return $this->requestStack->getMainRequest()?->headers->get(key: 'X-Request-Id');
    }
}
