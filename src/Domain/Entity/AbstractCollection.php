<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Override;
use Traversable;

abstract class AbstractCollection implements Countable, IteratorAggregate
{
    /** @var array<int, mixed> */
    protected array $items = [];

    /** @var int<0, max> */
    protected int $count = 0;

    /**
     * @param array<int, mixed> $items
     * @param int<0, max>|null $count
     */
    public function __construct(array $items = [], ?int $count = null)
    {
        $this->items = $items;
        $this->count = $count ?? count($this->items);
    }

    #[Override]
    public function count(): int
    {
        return $this->count;
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
