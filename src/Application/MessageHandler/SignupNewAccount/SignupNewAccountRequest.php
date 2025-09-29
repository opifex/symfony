<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Domain\Common\LocaleCode;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
#[AsMessage]
final class SignupNewAccountRequest
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\PasswordStrength]
        public readonly string $password = '',

        #[Assert\Locale]
        #[Assert\Choice(callback: [LocaleCode::class, 'values'])]
        public readonly string $locale = LocaleCode::EnUs->value,
    ) {
    }
}
