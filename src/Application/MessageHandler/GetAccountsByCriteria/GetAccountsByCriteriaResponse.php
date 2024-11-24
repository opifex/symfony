<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Traversable;

#[Exclude]
final class GetAccountsByCriteriaResponse implements Countable, IteratorAggregate
{
    public function __construct(private AccountCollection $accounts)
    {
    }

    #[Override]
    public function count(): int
    {
        return $this->accounts->count();
    }

    /**
     * @return Traversable<int, GetAccountsByCriteriaItem>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        /** @var Account $account */
        foreach ($this->accounts as $account) {
            yield new GetAccountsByCriteriaItem($account);
        }
    }
}
