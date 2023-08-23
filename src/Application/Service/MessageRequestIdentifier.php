<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\RequestIdentifierInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

final class MessageRequestIdentifier implements RequestIdentifierInterface
{
    private ?string $identifier = null;

    public function identify(?Request $request = null, ?string $identifier = null): string
    {
        $identifier ??= $request?->headers->get($this->key()) ?? '';
        $this->identifier = Uuid::isValid($identifier) ? $identifier : $this->identifier;
        $this->identifier ??= Uuid::v4()->toRfc4122();

        return $this->identifier;
    }

    public function key(): string
    {
        return 'X-Request-Id';
    }
}
