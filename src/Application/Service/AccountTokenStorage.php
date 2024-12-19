<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountTokenStorageInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountUnauthorizedException;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountTokenStorage implements AccountTokenStorageInterface
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Override]
    public function getAccount(): Account
    {
        $userIdentifier = $this->tokenStorage->getToken()?->getUserIdentifier();

        if (!$userIdentifier) {
            throw new AccountUnauthorizedException(
                message: 'An authentication exception occurred.',
            );
        }

        return $this->accountRepository->findOneByUuid($userIdentifier);
    }
}
