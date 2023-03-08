<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

use App\Domain\Contract\Entity\EntityInterface;
use App\Domain\Entity\EntityDateTimeTrait;
use App\Domain\Entity\EntityUuidTrait;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Account implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityDateTimeTrait;
    use EntityUuidTrait;

    #[Groups([self::GROUP_INDEX, self::GROUP_VIEW])]
    protected string $email = '';

    protected string $password = '';

    /**
     * @var string[]
     */
    #[Assert\Choice(choices: AccountRole::LIST, multiple: true)]
    #[Groups([self::GROUP_INDEX, self::GROUP_VIEW])]
    protected array $roles = [];

    #[Assert\Choice(choices: AccountStatus::LIST)]
    #[Groups([self::GROUP_INDEX, self::GROUP_VIEW])]
    protected string $status = AccountStatus::CREATED;

    /**
     * @param string[] $roles
     */
    public function __construct(string $email, array $roles = [])
    {
        $this->email = $email;
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->uuid ?? '';
    }
}
