<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use Symfony\Component\Validator\Constraints as Assert;

final class SignupNewAccountCommand
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\PasswordStrength]
        public readonly string $password = '',

        #[Assert\Length(min: 2, max: 2)]
        #[Assert\Locale]
        #[Assert\Regex(pattern: '/^[a-z]+$/', message: 'This value should be in lowercase.')]
        public readonly string $locale = 'en',
    ) {
    }
}
