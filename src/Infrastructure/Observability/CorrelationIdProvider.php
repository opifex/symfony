<?php

declare(strict_types=1);

namespace App\Infrastructure\Observability;

use Override;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\ResetInterface;

final class CorrelationIdProvider implements ResetInterface
{
    private ?string $correlationId = null;

    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId ??= Uuid::v4()->toString();
    }

    public function getHttpHeaderName(): string
    {
        return 'X-Correlation-Id';
    }

    #[Override]
    public function reset(): void
    {
        $this->correlationId = null;
    }
}
