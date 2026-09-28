<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use App\Domain\Foundation\Event\EventInterface;
use NoDiscard;

trait DomainEventsTrait
{
    use ImmutableCloneTrait;

    #[NoDiscard]
    private function withEvents(EventInterface ...$events): static
    {
        return $this->withFields(['events' => [...$this->events, ...$events]]);
    }

    /**
     * @return EventInterface[]
     */
    #[NoDiscard]
    public function releaseEvents(): array
    {
        return $this->events;
    }
}
