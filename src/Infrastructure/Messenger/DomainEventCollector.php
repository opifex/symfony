<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Domain\Foundation\Event\EventInterface;
use Override;
use Symfony\Contracts\Service\ResetInterface;

final class DomainEventCollector implements ResetInterface
{
    /**
     * @var EventInterface[]
     */
    private array $events = [];

    public function collect(EventInterface ...$events): void
    {
        $this->events = [...$this->events, ...$events];
    }

    /**
     * @return EventInterface[]
     */
    public function releaseEvents(): array
    {
        return array_splice(array: $this->events, offset: 0);
    }

    #[Override]
    public function reset(): void
    {
        $this->releaseEvents();
    }
}
