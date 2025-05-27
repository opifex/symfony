<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
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
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $account = $this->accountEntityRepository->findOneByEmail($identifier);

        if (!$account instanceof Account) {
            throw new UserNotFoundException();
        }

        return new PasswordAuthenticatedUser(
            userIdentifier: $account->getId()->toString(),
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
