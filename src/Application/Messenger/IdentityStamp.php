<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class IdentityStamp implements StampInterface
{
    public function __construct(private ?string $identifier)
    {
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}
