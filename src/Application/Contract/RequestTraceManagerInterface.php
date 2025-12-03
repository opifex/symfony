<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface RequestTraceManagerInterface
{
    public function setCorrelationId(string $correlationId): void;

    public function getCorrelationId(): string;
}
