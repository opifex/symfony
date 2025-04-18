<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\SearchPagination;
use App\Domain\Exception\AuthorizationForbiddenException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetAccountsByCriteriaRequest $message): GetAccountsByCriteriaResponse
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $searchPagination = new SearchPagination($message->page, $message->limit);
        $searchCriteria = new AccountSearchCriteria($message->email, $message->status, $searchPagination);

        $accountSearchResult = $this->accountRepository->findByCriteria($searchCriteria);

        return GetAccountsByCriteriaResponse::create($accountSearchResult, $searchPagination);
    }
}
