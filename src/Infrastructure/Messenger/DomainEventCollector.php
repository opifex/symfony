<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use Override;
use Symfony\Contracts\Service\ResetInterface;

final class DomainEventCollector implements ResetInterface
{
    /**
     * @var object[]
     */
    private array $events = [];

    public function collect(object ...$events): void
    {
        $this->events = [...$this->events, ...$events];
    }

    /**
     * @return object[]
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
