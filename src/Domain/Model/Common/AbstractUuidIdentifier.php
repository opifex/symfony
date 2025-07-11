<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
abstract class AbstractUuidIdentifier
{
    final protected function __construct(
        private readonly string $uuid,
    ) {
    }

    public static function generate(): static
    {
        return new static(Uuid::v7()->toString());
    }

    public static function fromString(string $uuid): static
    {
        return new static($uuid);
    }

    public function toString(): string
    {
        return $this->uuid;
    }
}
