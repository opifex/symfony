<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class GetAccountsByCriteriaResponse implements Countable, IteratorAggregate
{
    /**
     * @param Traversable<int, Account> $accounts
     */
    public function __construct(private Traversable $accounts)
    {
    }

    public function count(): int
    {
        return is_countable($this->accounts) ? count($this->accounts) : 0;
    }

    public function getIterator(): Traversable
    {
        $callback = fn(Account $account) => new GetAccountsByCriteriaItem($account);

        return new ArrayIterator(array_map(callback: $callback, array: iterator_to_array($this->accounts)));
    }
}
