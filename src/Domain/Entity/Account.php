<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected ?string $uuid = null;

    protected string $email = '';

    protected string $password = '';

    protected string $status = AccountStatus::CREATED;

    /** @var string[] */
    protected array $roles = [];

    protected ?DateTimeImmutable $createdAt = null;

    protected ?DateTimeImmutable $updatedAt = null;

    /**
     * @param string[] $roles
     */
    public function __construct(string $email, array $roles = [])
    {
        $this->email = $email;
        $this->roles = $roles;
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUserIdentifier(): string
    {
        return $this->uuid ?? '';
    }

    public function eraseCredentials(): void
    {
    }

    public function prePersistDateTime(): void
    {
        $datetime = new DateTimeImmutable();
        $this->createdAt = $datetime;
        $this->updatedAt = $datetime;
    }

    public function preUpdateDateTime(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
