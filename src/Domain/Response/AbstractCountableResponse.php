<?php

declare(strict_types=1);

namespace App\Domain\Response;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

abstract class AbstractCountableResponse implements Countable, IteratorAggregate
{
    private readonly int $count;

    private readonly ArrayIterator $iterator;

    /**
     * @param iterable<int, object> $items
     */
    public function __construct(iterable $items, callable $callback)
    {
        $this->count = is_countable($items) ? count($items) : 0;
        $array = $items instanceof Traversable ? iterator_to_array($items) : [];
        $this->iterator = new ArrayIterator(array_map($callback, $array));
    }

    public function count(): int
    {
        return $this->count;
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}
