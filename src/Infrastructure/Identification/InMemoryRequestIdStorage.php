<?php

declare(strict_types=1);

namespace App\Infrastructure\Identification;

use App\Application\Contract\RequestIdStorageInterface;
use Override;
use Symfony\Contracts\Service\ResetInterface;

final class InMemoryRequestIdStorage implements RequestIdStorageInterface, ResetInterface
{
    private ?string $requestId = null;

    #[Override]
    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
    }

    #[Override]
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    #[Override]
    public function reset(): void
    {
        $this->requestId = null;
    }
}
