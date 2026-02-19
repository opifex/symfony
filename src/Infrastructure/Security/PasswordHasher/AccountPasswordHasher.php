<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\PasswordHasher;

use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Infrastructure\Security\AuthenticatedUser\PasswordAuthenticatedUser;
use Override;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class AccountPasswordHasher implements AccountPasswordHasherInterface
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    #[Override]
    public function hash(#[SensitiveParameter] string $plainPassword): PasswordHash
    {
        return PasswordHash::fromString($this->getPasswordHasher()->hash($plainPassword));
    }

    private function getPasswordHasher(): PasswordHasherInterface
    {
        return $this->passwordHasherFactory->getPasswordHasher(
            user: PasswordAuthenticatedUser::class,
        );
    }
}
