<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountPasswordHasherInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class AccountPasswordHasher implements AccountPasswordHasherInterface
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    #[Override]
    public function hash(#[SensitiveParameter] string $plainPassword): string
    {
        return $this->getPasswordHasher()->hash($plainPassword);
    }

    private function getPasswordHasher(): PasswordHasherInterface
    {
        return $this->passwordHasherFactory->getPasswordHasher(
            user: PasswordAuthenticatedUserInterface::class,
        );
    }
}
