<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\RequestTraceManagerInterface;
use Override;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\ResetInterface;

final class RequestTraceManager implements RequestTraceManagerInterface, ResetInterface
{
    private ?string $correlationId = null;

    #[Override]
    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    #[Override]
    public function getCorrelationId(): string
    {
        return $this->correlationId ??= Uuid::v4()->toString();
    }

    #[Override]
    public function reset(): void
    {
        $this->correlationId = null;
    }
}
