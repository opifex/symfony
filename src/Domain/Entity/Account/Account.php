<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

use App\Domain\Entity\EntityDateTimeTrait;
use App\Domain\Entity\EntityUuidTrait;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityDateTimeTrait;
    use EntityUuidTrait;

    protected string $email = '';

    protected string $password = '';

    protected string $status = AccountStatus::CREATED;

    /**
     * @var string[]
     */
    protected array $roles = [];

    /**
     * @param string[] $roles
     */
    public function __construct(string $email, array $roles = [])
    {
        $this->email = $email;
        $this->roles = $roles;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->uuid ?? '';
    }

    public function eraseCredentials(): void
    {
    }
}
