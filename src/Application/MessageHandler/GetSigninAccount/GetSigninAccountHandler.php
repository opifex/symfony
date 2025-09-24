<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Model\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetSigninAccountRequest $request): GetSigninAccountResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: Role::User);

        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier()
            ?? throw AuthorizationRequiredException::create();

        $account = $this->accountEntityRepository->findOneById($userIdentifier)
            ?? throw AccountNotFoundException::create();

        return GetSigninAccountResult::success($account);
    }
}
