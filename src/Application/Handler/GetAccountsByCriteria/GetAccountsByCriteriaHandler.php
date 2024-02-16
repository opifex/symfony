<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\SearchPagination;
use App\Domain\Entity\SearchSorting;
use App\Domain\Entity\SortingOrder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountsByCriteriaQuery $message): GetAccountsByCriteriaResponse
    {
        $accounts = $this->accountRepository->findByCriteria(
            new AccountSearchCriteria(
                email: $message->email,
                status: $message->status,
                sorting: new SearchSorting($message->sort, SortingOrder::fromValue($message->order)),
                pagination: new SearchPagination($message->limit, $message->offset),
            ),
        );

        return new GetAccountsByCriteriaResponse($accounts);
    }
}
