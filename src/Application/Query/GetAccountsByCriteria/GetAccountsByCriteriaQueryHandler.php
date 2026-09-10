<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetAccountsByCriteriaQueryHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(GetAccountsByCriteriaQuery $query): GetAccountsByCriteriaQueryResult
    {
        $searchResult = $this->accountRepository->findByCriteria(
            accountEmail: $query->email,
            accountStatus: $query->status,
            pageNumber: $query->page,
            pageSize: $query->limit,
        );

        return GetAccountsByCriteriaQueryResult::success($searchResult);
    }
}
