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
        $accounts = $accountSearchResult->getResultItems();

        return new self(
            payload: [
                'meta' => [
                    'current_page' => $accountSearchResult->getCurrentPageNumber(),
                    'items_per_page' => $accountSearchResult->getItemsPerPageAmount(),
                    'total_items' => $accountSearchResult->getTotalResultsCount(),
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
