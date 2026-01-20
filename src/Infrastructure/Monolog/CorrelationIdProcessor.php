<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use App\Infrastructure\Observability\CorrelationIdProvider;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class CorrelationIdProcessor
{
    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $correlationId = $this->correlationIdProvider->getCorrelationId();
        $record->extra['correlation_id'] = $correlationId;

        return $record;
    }
}
