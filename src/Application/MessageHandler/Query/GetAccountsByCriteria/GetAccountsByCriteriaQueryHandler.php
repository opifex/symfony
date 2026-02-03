<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetAccountsByCriteria;

use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Foundation\SearchPagination;
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
        $searchPagination = new SearchPagination($query->page, $query->limit);
        $searchCriteria = new AccountSearchCriteria($query->email, $query->status, $searchPagination);

        $accountSearchResult = $this->accountEntityRepository->findByCriteria($searchCriteria);

        return GetAccountsByCriteriaQueryResult::success($accountSearchResult, $searchPagination);
    }
}
