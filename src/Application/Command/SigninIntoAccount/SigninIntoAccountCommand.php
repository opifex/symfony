<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use SensitiveParameter;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SigninIntoAccountCommand
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public string $email = '',

        #[Assert\NotBlank]
        #[SensitiveParameter]
        public string $password = '',
    ) {
    }
}
