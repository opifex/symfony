<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\AccountInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Uid\Uuid;

final class AccountUserProvider implements UserProviderInterface
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            return match (Uuid::isValid($identifier)) {
                true => $this->accountRepository->findOneByUuid($identifier),
                false => $this->accountRepository->findOneByEmail($identifier),
            };
        } catch (AccountNotFoundException $e) {
            throw new UserNotFoundException(previous: $e);
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass(string $class): bool
    {
        return is_subclass_of($class, class: AccountInterface::class);
    }
}
