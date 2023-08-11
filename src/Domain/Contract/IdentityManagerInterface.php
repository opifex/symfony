<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface IdentityManagerInterface
{
    public function getIdentifier(): ?string;

    public function setIdentifier(?string $identifier): self;
}
