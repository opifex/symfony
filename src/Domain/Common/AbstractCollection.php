<?php

declare(strict_types=1);

namespace App\Domain\Common;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Traversable;

#[Exclude]
abstract class AbstractCollection implements Countable, IteratorAggregate
{
    /** @var array<int|string, mixed> $elements */
    protected array $elements = [];

    /**
     * @param mixed ...$elements
     */
    public function __construct(
        mixed ...$elements,
    ) {
        $this->elements = $elements;
    }

    #[Override]
    public function count(): int
    {
        return count($this->elements);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }
}
