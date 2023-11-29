<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\AccountInterface;
use App\Domain\Entity\AccountStatus;
use Override;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccountUserChecker implements UserCheckerInterface
{
    #[Override]
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof AccountInterface && $user->getStatus() !== AccountStatus::VERIFIED) {
            throw new CustomUserMessageAccountStatusException(
                message: 'The presented account is not verified.',
            );
        }
    }

    #[Override]
    public function checkPreAuth(UserInterface $user): void
    {
    }
}
