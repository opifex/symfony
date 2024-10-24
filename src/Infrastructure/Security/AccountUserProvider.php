<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountNotFoundException;
use Override;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class AccountUserProvider implements UserProviderInterface
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            return $this->accountRepository->findOneByEmail($identifier);
        } catch (AccountNotFoundException $e) {
            throw new UserNotFoundException(previous: $e);
        }
    }

    #[Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException();
    }

    #[Override]
    public function supportsClass(string $class): bool
    {
        return is_a($class, class: Account::class, allow_string: true);
    }
}
