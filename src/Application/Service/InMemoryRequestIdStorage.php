<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\RequestIdStorageInterface;
use Override;
use Symfony\Component\Uid\Uuid;

final class InMemoryRequestIdStorage implements RequestIdStorageInterface
{
    private ?string $requestId = null;

    #[Override]
    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }

    #[Override]
    public function getRequestId(): string
    {
        return $this->requestId ??= Uuid::v4()->toRfc4122();
    }
}
