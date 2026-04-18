<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

use DomainException;

abstract readonly class AbstractUuidIdentifier
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    final private function __construct(
        private string $uuid,
    ) {
    }

    public static function fromString(string $uuid): static
    {
        if (preg_match(pattern: self::UUID_PATTERN, subject: $uuid) !== 1) {
            throw new DomainException(message: 'Invalid UUID identifier provided.');
        }

        return new static($uuid);
    }

    public function toString(): string
    {
        return $this->uuid;
    }
}
