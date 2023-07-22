<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class AccountCollection implements Countable, IteratorAggregate
{
    /** @var Account[] */
    private readonly array $accounts;

    private readonly int $count;

    /**
     * @param Countable&IteratorAggregate<int, Account> $accounts
     */
    public function __construct(Countable&IteratorAggregate $accounts)
    {
        $this->accounts = iterator_to_array($accounts);
        $this->count = count($accounts);
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return Traversable<int, Account>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->accounts);
    }
}
