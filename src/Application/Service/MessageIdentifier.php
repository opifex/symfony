<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\MessageIdentifierInterface;
use Symfony\Component\Uid\Uuid;

final class MessageIdentifier implements MessageIdentifierInterface
{
    private ?Uuid $identifier = null;

    public function validate(string $identifier): bool
    {
        return Uuid::isValid($identifier);
    }

    public function replace(string $identifier): void
    {
        $this->identifier = Uuid::fromString($identifier);
    }

    public function identify(): string
    {
        return ($this->identifier ??= Uuid::v4())->toRfc4122();
    }
}
