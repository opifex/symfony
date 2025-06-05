<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\Role;
use App\Domain\Model\SearchPagination;
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
        if (!$this->authorizationTokenManager->checkPermission(access: Role::Admin->toString())) {
            throw AuthorizationForbiddenException::create();
        }

        $searchPagination = new SearchPagination($request->page, $request->limit);
        $searchCriteria = new AccountSearchCriteria($request->email, $request->status, $searchPagination);

        $accountSearchResult = $this->accountEntityRepository->findByCriteria($searchCriteria);

        return GetAccountsByCriteriaResult::success($accountSearchResult, $searchPagination);
    }
}
