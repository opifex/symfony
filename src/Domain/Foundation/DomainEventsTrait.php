<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use NoDiscard;

trait DomainEventsTrait
{
    use ImmutableCloneTrait;

    #[NoDiscard]
    private function withEvents(object ...$events): static
    {
        return $this->withFields(['events' => [...$this->events, ...$events]]);
    }

    /**
     * @return object[]
     */
    #[NoDiscard]
    public function releaseEvents(): array
    {
        return $this->events;
    }
}
