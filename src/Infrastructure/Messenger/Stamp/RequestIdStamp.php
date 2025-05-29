<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Stamp;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Stamp\StampInterface;

#[Exclude]
final class RequestIdStamp implements StampInterface
{
    public function __construct(
        private readonly string $requestId,
    ) {
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
