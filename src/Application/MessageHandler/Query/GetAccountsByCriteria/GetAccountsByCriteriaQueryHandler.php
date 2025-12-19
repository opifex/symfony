<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetAccountsByCriteria;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Foundation\SearchPagination;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetAccountsByCriteriaQuery $request): GetAccountsByCriteriaQueryResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        $searchPagination = new SearchPagination($request->page, $request->limit);
        $searchCriteria = new AccountSearchCriteria($request->email, $request->status, $searchPagination);

        $accountSearchResult = $this->accountEntityRepository->findByCriteria($searchCriteria);

        return GetAccountsByCriteriaQueryResult::success($accountSearchResult, $searchPagination);
    }
}
