<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Override;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordAuthenticatedUserChecker implements UserCheckerInterface
{
    #[Override]
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof PasswordAuthenticatedUser && !$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException(
                message: 'The presented account is not activated.',
            );
        }
    }

    #[Override]
    public function checkPreAuth(UserInterface $user): void
    {
        // Nothing to do
    }
}
