<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
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
        foreach ($accounts as $account) {
            if (!$account instanceof Account) {
                throw new InvalidArgumentException(message: 'The array must contain Account objects.');
            }
        }

        $this->accounts = $accounts instanceof Traversable ? iterator_to_array($accounts) : $accounts;
        $this->count = is_countable($accounts) ? count($accounts) : 0;
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
