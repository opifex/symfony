<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Entity\AccountCollection;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Traversable;

#[WithHttpStatus(statusCode: Response::HTTP_OK)]
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
        foreach ($this->accounts as $account) {
            yield new GetAccountsByCriteriaItem($account);
        }
    }
}
