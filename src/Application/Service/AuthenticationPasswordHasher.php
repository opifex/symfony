<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\AuthenticationPasswordHasherInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class AuthenticationPasswordHasher implements AuthenticationPasswordHasherInterface
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
