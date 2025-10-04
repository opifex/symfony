<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface RequestTraceManagerInterface
{
    public function setTraceId(string $traceId): void;

    public function getTraceId(): string;
}
