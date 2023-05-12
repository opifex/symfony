<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
final class MonologProcessor
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();
        $identifier = $request?->headers->get(key: 'X-Request-Id');

        $record->extra['identifier'] = $identifier;

        return $record;
    }
}
