<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\RequestIdentifierInterface;
use Symfony\Component\Uid\Uuid;

final class MessageRequestIdentifier implements RequestIdentifierInterface
{
    private ?string $identifier = null;

    public function setIdentifier(string $identifier): void
    {
        if ($identifier !== $this->identifier && Uuid::isValid($identifier)) {
            $this->identifier = $identifier;
        }
    }

    public function getIdentifier(): string
    {
        return $this->identifier ??= Uuid::v4()->toRfc4122();
    }
}
