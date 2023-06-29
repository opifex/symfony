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
        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        public readonly string $password = '',
    ) {
    }
}
