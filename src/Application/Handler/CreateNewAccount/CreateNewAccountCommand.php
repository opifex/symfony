<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\Locale;
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
        #[Assert\PasswordStrength]
        public readonly string $password = '',

        #[Assert\Choice(choices: Locale::LOCALES)]
        public readonly string $locale = Locale::EN,

        #[Assert\Choice(choices: AccountRole::ROLES, multiple: true)]
        #[Assert\NotBlank]
        public readonly array $roles = [],
    ) {
    }
}
