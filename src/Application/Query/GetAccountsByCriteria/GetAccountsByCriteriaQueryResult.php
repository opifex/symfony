<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\Account;
use App\Domain\Account\AccountSearchResult;
use App\Domain\Foundation\AbstractHandlerResult;
use App\Domain\Foundation\SearchPagination;

final class GetAccountsByCriteriaQueryResult extends AbstractHandlerResult
{
    public static function success(AccountSearchResult $accountSearchResult, SearchPagination $searchPagination): self
    {
        return new self(
            payload: [
                'meta' => [
                    'current_page' => $searchPagination->getPage(),
                    'items_per_page' => $searchPagination->getLimit(),
                    'total_items' => $accountSearchResult->getTotalResultCount(),
                ],
                'data' => array_map(
                    callback: static fn(Account $account): array => [
                        'id' => $account->getId()->toString(),
                        'email' => $account->getEmail()->toString(),
                        'locale' => $account->getLocale()->toString(),
                        'status' => $account->getStatus()->toString(),
                        'roles' => $account->getRoles()->toArray(),
                        'created_at' => $account->getCreatedAt()->toAtomString(),
                    ],
                    array: $accountSearchResult->getAccounts(),
                ),
            ],
        );
    }
}
