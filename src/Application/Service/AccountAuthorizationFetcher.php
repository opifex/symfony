<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountAuthorizationFetcherInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AuthorizationToken;
use App\Domain\Exception\AccountUnauthorizedException;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountAuthorizationFetcher implements AccountAuthorizationFetcherInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    #[Override]
    public function fetchAccount(): Account
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof Account) {
            throw new AccountUnauthorizedException(
                message: 'An authentication exception occurred.',
            );
        }

        return $user;
    }

    #[Override]
    public function fetchToken(): AuthorizationToken
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof AuthorizationToken) {
            throw new AccountUnauthorizedException(
                message: 'An authentication exception occurred.',
            );
        }

        return $token;
    }
}
