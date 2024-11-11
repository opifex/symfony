<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
final class SigninIntoAccountRequest
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
