<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Domain\Entity\AccountRole;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateNewAccountCommand
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        public readonly string $password = '',

        #[Assert\Choice(choices: AccountRole::ROLES, multiple: true)]
        #[Assert\NotBlank]
        public readonly array $roles = [],
    ) {
    }
}
