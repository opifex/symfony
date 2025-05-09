<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Contract\AccountEntityBuilderInterface;
use App\Domain\Contract\AccountEntityInterface;
use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use DateTimeImmutable;

final class AccountEntityBuilder implements AccountEntityBuilderInterface
{
    public function build(string $email, string $hashedPassword, string $locale): AccountEntityInterface
    {
        return new AccountEntity(
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $hashedPassword,
            locale: $locale,
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }
}
