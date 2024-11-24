<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Traversable;

#[Exclude]
abstract class AbstractCollection implements Countable, IteratorAggregate
{
    /** @var array<int|string, mixed> $items */
    protected array $items = [];

    /**
     * @param mixed ...$items
     */
    public function __construct(mixed ...$items)
    {
        $this->items = $items;
    }

    #[Override]
    public function count(): int
    {
        return count($this->items);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
