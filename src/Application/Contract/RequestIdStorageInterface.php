<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface RequestIdStorageInterface
{
    public function setRequestId(?string $requestId): void;

    public function getRequestId(): ?string;
}
