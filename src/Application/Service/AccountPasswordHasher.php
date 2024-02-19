<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Entity\Account;
use Override;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final class AccountPasswordHasher implements AccountPasswordHasherInterface
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory)
    {
    }

    #[Override]
    public function hash(#[SensitiveParameter] string $password): string
    {
        return $this->passwordHasherFactory->getPasswordHasher(user: Account::class)->hash($password);
    }
}
