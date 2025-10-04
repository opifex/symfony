<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\RequestTraceManagerInterface;
use Override;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\ResetInterface;

final class RequestTraceManager implements RequestTraceManagerInterface, ResetInterface
{
    private ?string $traceId = null;

    #[Override]
    public function setTraceId(string $traceId): void
    {
        $this->traceId = $traceId;
    }

    #[Override]
    public function getTraceId(): string
    {
        return $this->traceId ??= Uuid::v4()->toString();
    }

    #[Override]
    public function reset(): void
    {
        $this->traceId = null;
    }
}
