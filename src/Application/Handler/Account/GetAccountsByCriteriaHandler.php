<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Message\Account\GetAccountsByCriteriaQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
class GetAccountsByCriteriaHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    /**
     * @return Account[]
     */
    public function __invoke(GetAccountsByCriteriaQuery $message): iterable
    {
        return $this->accountRepository->findByCriteria(
            criteria: $message->criteria,
            sort: $message->sort,
            limit: $message->limit,
            offset: $message->offset,
        );
    }
}
