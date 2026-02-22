<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\Account;
use App\Domain\Foundation\SearchPaginationResult;
use JsonSerializable;
use Override;

final class GetAccountsByCriteriaQueryResult implements JsonSerializable
{
    private function __construct(
        private readonly mixed $payload = null,
    ) {
    }

    public static function success(SearchPaginationResult $accountSearchResult): self
    {
        /** @var Account[] $accounts */
        $accounts = $accountSearchResult->resultItemsList;

        return new self(
            payload: [
                'meta' => [
                    'current_page' => $accountSearchResult->currentPageNumber,
                    'items_per_page' => $accountSearchResult->itemsPerPageAmount,
                    'total_items' => $accountSearchResult->totalResultsCount,
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
