<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class GetAccountsByCriteriaResponse implements Countable, IteratorAggregate
{
    public function __construct(private AccountCollection $accounts)
    {
    }

    public function count(): int
    {
        return $this->accounts->count();
    }

    public function getIterator(): Traversable
    {
        $callback = fn(Account $account) => new GetAccountsByCriteriaItem($account);

        return new ArrayIterator(array_map(callback: $callback, array: $this->accounts->all()));
    }
}
