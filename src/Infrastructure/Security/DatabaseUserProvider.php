<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use Override;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<PasswordAuthenticatedUser>
 */
final class DatabaseUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $account = $this->accountRepository->findOneByEmail($identifier);
        } catch (AccountNotFoundException $e) {
            throw new UserNotFoundException(previous: $e);
        }

        return new PasswordAuthenticatedUser(
            userIdentifier: $account->getUuid(),
            password: $account->getPassword(),
            roles: $account->getRoles(),
            enabled: $account->isActive(),
        );
    }

    #[Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException();
    }

    #[Override]
    public function supportsClass(string $class): bool
    {
        return is_a($class, class: PasswordAuthenticatedUser::class, allow_string: true);
    }
}
