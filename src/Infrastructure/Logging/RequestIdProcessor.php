<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
final class RequestIdProcessor
{
    private ?string $requestId = null;

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $mainRequest = $this->requestStack->getMainRequest();
        $this->requestId ??= $mainRequest?->headers->get(key: 'X-Request-Id');

        $record->extra['uuid'] = $this->requestId ?? '';

        return $record;
    }
}
