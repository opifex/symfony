<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Account;
use Symfony\Component\Uid\Uuid;

final class AccountEntityBuilder
{
    private string $emailAddress = '';

    private string $hashedPassword = '';

    private string $defaultLocale = '';

    /**
     * @var string[]
     */
    private array $accessRoles = [];

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function setHashedPassword(string $hashedPassword): self
    {
        $this->hashedPassword = $hashedPassword;

        return $this;
    }

    public function setDefaultLocale(string $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @param string[] $accessRole
     */
    public function setAccessRoles(array $accessRole): self
    {
        $this->accessRoles = $accessRole;

        return $this;
    }

    public function getAccount(): Account
    {
        return new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: $this->emailAddress,
            password: $this->hashedPassword,
            locale: $this->defaultLocale,
            roles: $this->accessRoles,
        );
    }
}
