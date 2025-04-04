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
final class GetAccountsByCriteriaResponse extends JsonResponse
{
    public static function create(AccountSearchResult $accountSearchResult, SearchPagination $searchPagination): self
    {
        /** @var Account[] $accounts */
        $accounts = iterator_to_array($accountSearchResult->getAccounts());

        return new self(
            data: [
                'meta' => [
                    'current_page' => $searchPagination->getPage(),
                    'items_per_page' => $searchPagination->getLimit(),
                    'total_items' => $accountSearchResult->getTotalResultCount(),
                ],
                'data' => array_map(
                    callback: fn(Account $account) => [
                        'uuid' => $account->getUuid(),
                        'email' => $account->getEmail(),
                        'locale' => $account->getLocale(),
                        'status' => $account->getStatus()->value,
                        'roles' => $account->getRoles()->toArray(),
                        'created_at' => $account->getCreatedAt()->format(format: DateTimeInterface::ATOM),
                    ],
                    array: $accounts,
                ),
            ],
            status: Response::HTTP_OK,
        );
    }
}
