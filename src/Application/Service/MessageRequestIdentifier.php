<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\RequestIdentifierInterface;
use Override;
use Symfony\Component\Uid\Uuid;

final class MessageRequestIdentifier implements RequestIdentifierInterface
{
    private ?string $identifier = null;

    #[Override]
    public function setIdentifier(string $identifier): void
    {
        if ($identifier !== $this->identifier && Uuid::isValid($identifier)) {
            $this->identifier = $identifier;
        }
    }

    #[Override]
    public function getIdentifier(): string
    {
        return $this->identifier ??= Uuid::v4()->toRfc4122();
    }
}
