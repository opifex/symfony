<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\IdentityManagerInterface;

class RequestIdentityManager implements IdentityManagerInterface
{
    private ?string $identifier = null;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }
}
