<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(GetAccountsByCriteriaQuery $query): GetAccountsByCriteriaQueryResult
    {
        $searchResult = $this->accountEntityRepository->findByCriteria(
            accountEmail: $query->email,
            accountStatus: $query->status,
            pageNumber: $query->page,
            pageSize: $query->limit,
        );

        return GetAccountsByCriteriaQueryResult::success($searchResult);
    }
}
