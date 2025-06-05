<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchResult;
use App\Domain\Model\SearchPagination;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetAccountsByCriteriaResult extends JsonResponse
{
    public static function success(AccountSearchResult $accountSearchResult, SearchPagination $searchPagination): self
    {
        return new self(
            data: [
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
            status: Response::HTTP_OK,
        );
    }
}
