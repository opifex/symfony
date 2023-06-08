<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Criteria\AccountSearchCriteria;
use App\Domain\Message\GetAccountsByCriteriaQuery;
use App\Domain\Response\GetAccountsByCriteriaResponse;
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
                order: $message->order,
                limit: $message->limit,
                offset: $message->offset,
            ),
        );

        return new GetAccountsByCriteriaResponse($accounts);
    }
}
