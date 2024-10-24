<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountTokenStorageInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountUnauthorizedException;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountTokenStorage implements AccountTokenStorageInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    #[Override]
    public function getAccount(): Account
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof Account) {
            throw new AccountUnauthorizedException(
                message: 'An authentication exception occurred.',
            );
        }

        return $user;
    }
}
