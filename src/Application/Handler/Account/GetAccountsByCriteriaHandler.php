<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Message\Account\GetAccountsByCriteriaQuery;
use App\Domain\Response\GetAccountsByCriteriaResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
final class GetAccountsByCriteriaHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountsByCriteriaQuery $message): GetAccountsByCriteriaResponse
    {
        $accounts = $this->accountRepository->findByCriteria(
            criteria: $message->criteria,
            sort: $message->sort,
            limit: $message->limit,
            offset: $message->offset,
        );

        return new GetAccountsByCriteriaResponse($accounts);
    }
}
