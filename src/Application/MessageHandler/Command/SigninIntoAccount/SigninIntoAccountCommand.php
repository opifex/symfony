<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\SigninIntoAccount;

use Symfony\Component\Validator\Constraints as Assert;

final class SigninIntoAccountCommand
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\NotBlank]
        public readonly string $password = '',
    ) {
    }
}
