<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapping\Default;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use DateTimeImmutable;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final class AccountFactory
{
    public static function create(string $email, #[SensitiveParameter] string $password): AccountEntity
    {
        return new AccountEntity(
            uuid: Uuid::v7()->toRfc4122(),
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $password,
            locale: 'en_US',
            roles: [AccountRole::User->value],
            status: AccountStatus::Created->value,
        );
    }
}
