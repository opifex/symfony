<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final class AccountFactory
{
    public static function createEntity(string $email, string $password, string $locale): AccountEntity
    {
        return new AccountEntity(
            uuid: Uuid::v7()->toRfc4122(),
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $password,
            locale: $locale,
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }
}
