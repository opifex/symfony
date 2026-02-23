<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\Account;
use App\Domain\Foundation\SearchResult;
use JsonSerializable;
use Override;

final class GetAccountsByCriteriaQueryResult implements JsonSerializable
{
    private function __construct(
        private readonly mixed $payload = null,
    ) {
    }

    public static function success(SearchResult $searchResult): self
    {
        /** @var Account[] $accounts */
        $accounts = $searchResult->items;

        return new self(
            payload: [
                'meta' => [
                    'current_page' => $searchResult->pageNumber,
                    'items_per_page' => $searchResult->pageSize,
                    'total_items' => $searchResult->totalCount,
                ],
                'data' => array_map(
                    callback: static fn(Account $account): array => [
                        'id' => $account->id->toString(),
                        'email' => $account->email->toString(),
                        'locale' => $account->locale->toString(),
                        'status' => $account->status->toString(),
                        'roles' => $account->roles->toArray(),
                        'created_at' => $account->createdAt->toAtomString(),
                    ],
                    array: $accounts,
                ),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
