<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Contract\AccountEntityBuilderInterface;
use App\Domain\Contract\AccountEntityInterface;
use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
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
