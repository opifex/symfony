<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Stamp;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Stamp\StampInterface;

#[Exclude]
final class RequestTraceStamp implements StampInterface
{
    public function __construct(
        private readonly string $traceId,
    ) {
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }
}
