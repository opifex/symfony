<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\IdentityManagerInterface;
use Symfony\Component\Uid\Uuid;

class RequestIdentityManager implements IdentityManagerInterface
{
    private ?Uuid $identifier = null;

    public function validateIdentifier(string $identifier): bool
    {
        return Uuid::isValid($identifier);
    }

    public function changeIdentifier(string $identifier): void
    {
        $this->identifier = Uuid::fromString($identifier);
    }

    public function extractIdentifier(): string
    {
        return ($this->identifier ??= Uuid::v4())->toRfc4122();
    }
}
