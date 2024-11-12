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
    private PasswordHasherInterface $passwordHasher;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasher = $passwordHasherFactory->getPasswordHasher(
            user: PasswordAuthenticatedUserInterface::class,
        );
    }

    #[Override]
    public function hash(#[SensitiveParameter] string $plainPassword): string
    {
        return $this->passwordHasher->hash($plainPassword);
    }
}
