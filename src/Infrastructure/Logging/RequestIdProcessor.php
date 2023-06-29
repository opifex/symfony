<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
final class RequestIdProcessor
{
    private ?string $uuid = null;

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $this->uuid ??= $this->requestStack->getMainRequest()?->headers->get(key: 'X-Request-Id');

        $record->extra['uuid'] = $this->uuid ?? '';

        return $record;
    }
}
