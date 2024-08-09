<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Entity\Account;
use Symfony\Component\Uid\Uuid;

final class AccountEntityBuilder
{
    private string $emailAddress = '';

    private string $plainPassword = '';

    private string $defaultLocale = '';

    /**
     * @var string[]
     */
    private array $accessRoles = [];

    public function __construct(private AccountPasswordHasherInterface $accountPasswordHasher)
    {
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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
            password: $this->accountPasswordHasher->hash($this->plainPassword),
            locale: $this->defaultLocale,
            roles: $this->accessRoles,
        );
    }
}
