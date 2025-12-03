<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use App\Application\Contract\RequestTraceManagerInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class RequestTraceProcessor
{
    public function __construct(
        private readonly RequestTraceManagerInterface $requestTraceManager,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $correlationId = $this->requestTraceManager->getCorrelationId();
        $record->extra['correlation_id'] = $correlationId;

        return $record;
    }
}
