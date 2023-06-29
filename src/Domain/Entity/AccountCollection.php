<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class AccountCollection implements Countable, IteratorAggregate
{
    /**
     * @var Account[]
     */
    private readonly array $accounts;

    private readonly int $count;

    /**
     * @param iterable<int, Account> $accounts
     */
    public function __construct(iterable $accounts)
    {
        $this->accounts = $accounts instanceof Traversable ? iterator_to_array($accounts) : $accounts;
        $this->count = $accounts instanceof Countable || is_array($accounts) ? count($accounts) : 0;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->accounts);
    }

    /**
     * @return array&array<int, Account>
     */
    public function all(): array
    {
        return $this->accounts;
    }
}
