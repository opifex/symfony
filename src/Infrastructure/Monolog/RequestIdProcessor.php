<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use App\Domain\Contract\RequestIdStorageInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class RequestIdProcessor
{
    public function __construct(
        private readonly RequestIdStorageInterface $requestIdStorage,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $requestId = $this->requestIdStorage->getRequestId();

        if ($requestId !== null) {
            $record->extra['identifier'] = $requestId;
        }

        return $record;
    }
}
