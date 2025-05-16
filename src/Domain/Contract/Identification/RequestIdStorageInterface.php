<?php

declare(strict_types=1);

namespace App\Domain\Contract\Identification;

interface RequestIdStorageInterface
{
    public function setRequestId(?string $requestId): void;

    public function getRequestId(): ?string;
}
