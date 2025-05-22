<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
#[AsMessage]
final class UpdateAccountByIdRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $id = '',

        #[Assert\Email]
        public readonly ?string $email = null,

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\PasswordStrength]
        public readonly ?string $password = null,

        #[Assert\Locale]
        #[Assert\Regex(pattern: '/^[a-z]{2}_[A-Z]{2}$/', message: 'This value is not a valid locale.')]
        public readonly ?string $locale = null,
    ) {
    }
}
