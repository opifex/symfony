<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Criteria\AccountSearchCriteria;
use App\Domain\Entity\SortingOrder;
use App\Domain\Message\Account\GetAccountsByCriteriaQuery;
use App\Domain\Response\Account\GetAccountsByCriteriaResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
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
                sort: $message->sort,
                order: SortingOrder::tryFrom($message->order),
                limit: $message->limit,
                offset: $message->offset,
            ),
        );

        return new GetAccountsByCriteriaResponse($accounts);
    }
}
