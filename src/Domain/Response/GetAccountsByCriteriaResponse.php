<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Account;

final class GetAccountsByCriteriaResponse extends AbstractCountableResponse
{
    public const GROUP_VIEW = __CLASS__ . ':view';

    /**
     * @param Account[] $items
     */
    public function __construct(iterable $items)
    {
        parent::__construct($items, fn(Account $account) => new GetAccountsByCriteriaItem($account));
    }
}
