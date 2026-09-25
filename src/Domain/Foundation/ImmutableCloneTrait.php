<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use NoDiscard;

trait ImmutableCloneTrait
{
    /**
     * @param array<string, mixed> $properties
     */
    #[NoDiscard]
    private function withFields(array $properties): static
    {
        return clone($this, $properties);
    }
}
