<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

trait ImmutableCloneTrait
{
    /**
     * @param array<string, mixed> $properties
     */
    private function withFields(array $properties): static
    {
        return clone($this, $properties);
    }
}
