<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountSearchResult;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Traversable;

#[Exclude]
final class GetAccountsByCriteriaResponse implements Countable, IteratorAggregate
{
    public function __construct(private readonly AccountSearchResult $accountSearchResult)
    {
    }

    public static function create(AccountSearchResult $accountSearchResult): self
    {
        return new self($accountSearchResult);
    }

    #[Override]
    public function count(): int
    {
        return $this->accountSearchResult->getTotalResultCount();
    }

    /**
     * @return Traversable<int, GetAccountsByCriteriaItem>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        /** @var Account $account */
        foreach ($this->accountSearchResult->getAccounts() as $account) {
            yield GetAccountsByCriteriaItem::create($account);
        }
    }
}
