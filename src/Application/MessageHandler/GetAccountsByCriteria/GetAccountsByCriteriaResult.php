<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchResult;
use App\Domain\Model\SearchPagination;
use DateTimeInterface;
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
                    'current_page' => $searchPagination->page,
                    'items_per_page' => $searchPagination->limit,
                    'total_items' => $accountSearchResult->totalResultCount,
                ],
                'data' => array_map(
                    callback: static fn(Account $account): array => [
                        'uuid' => $account->id,
                        'email' => $account->email,
                        'locale' => $account->locale,
                        'status' => $account->status,
                        'roles' => $account->roles,
                        'created_at' => $account->createdAt->format(format: DateTimeInterface::ATOM),
                    ],
                    array: $accountSearchResult->accounts,
                ),
            ],
            status: Response::HTTP_OK,
        );
    }
}
