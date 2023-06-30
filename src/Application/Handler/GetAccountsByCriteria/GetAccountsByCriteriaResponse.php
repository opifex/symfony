<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Entity\AccountCollection;
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
        foreach ($this->accounts as $account) {
            yield new GetAccountsByCriteriaItem($account);
        }
    }
}
