<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Application\Handler\AbstractCountableResponse;
use App\Domain\Entity\Account;

final class GetAccountsByCriteriaResponse extends AbstractCountableResponse
{
    /**
     * @param Account[] $items
     */
    public function __construct(iterable $items)
    {
        parent::__construct($items, fn(Account $account) => new GetAccountsByCriteriaItem($account));
    }
}
