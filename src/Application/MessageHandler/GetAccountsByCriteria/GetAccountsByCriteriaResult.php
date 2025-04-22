<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountSearchResult;
use App\Domain\Entity\SearchPagination;
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
                    'current_page' => $searchPagination->getPage(),
                    'items_per_page' => $searchPagination->getLimit(),
                    'total_items' => $accountSearchResult->getTotalResultCount(),
                ],
                'data' => array_map(
                    callback: static fn(Account $account): array => [
                        'uuid' => $account->getUuid(),
                        'email' => $account->getEmail(),
                        'locale' => $account->getLocale(),
                        'status' => $account->getStatus(),
                        'roles' => $account->getRoles(),
                        'created_at' => $account->getCreatedAt()->format(format: DateTimeInterface::ATOM),
                    ],
                    array: $accountSearchResult->getAccounts(),
                ),
            ],
            status: Response::HTTP_OK,
        );
    }
}
