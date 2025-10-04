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
        $traceId = $this->requestTraceManager->getTraceId();
        $record->extra['identifier'] = $traceId;

        return $record;
    }
}
