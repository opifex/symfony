<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Contract\AccountEntityBuilderInterface;
use App\Domain\Contract\AccountEntityInterface;
use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final class AccountEntityBuilder implements AccountEntityBuilderInterface
{
    private string $email = '';

    private string $password = '';

    private string $locale = '';

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    public function withLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function build(): AccountEntityInterface
    {
        return new AccountEntity(
            uuid: Uuid::v7()->toRfc4122(),
            createdAt: new DateTimeImmutable(),
            email: $this->email,
            password: $this->password,
            locale: $this->locale,
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }
}
