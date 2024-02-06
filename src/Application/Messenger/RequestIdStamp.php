<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class RequestIdStamp implements StampInterface
{
    public function __construct(private string $requestId)
    {
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
