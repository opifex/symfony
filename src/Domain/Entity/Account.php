<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\AccountInterface;
use DateTimeImmutable;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Account implements AccountInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    protected ?string $uuid = null;

    protected ?string $password = null;

    protected string $status = AccountStatus::CREATED;

    protected DateTimeImmutable $createdAt;

    /**
     * @param string[] $roles
     */
    public function __construct(protected string $email, protected array $roles = [])
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUuid(): string
    {
        return $this->uuid ?? '';
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

    public function getPassword(): ?string
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUuid();
    }

    public function eraseCredentials(): void
    {
    }
}
