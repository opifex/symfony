<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Common\SearchPagination;
use App\Domain\Security\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetAccountsByCriteriaRequest $request): GetAccountsByCriteriaResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: Role::Admin);

        $searchPagination = new SearchPagination($request->page, $request->limit);
        $searchCriteria = new AccountSearchCriteria($request->email, $request->status, $searchPagination);

        $accountSearchResult = $this->accountEntityRepository->findByCriteria($searchCriteria);

        return GetAccountsByCriteriaResult::success($accountSearchResult, $searchPagination);
    }
}
