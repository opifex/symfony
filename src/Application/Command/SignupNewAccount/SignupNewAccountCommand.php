<?php

declare(strict_types=1);

namespace App\Application\Command\SignupNewAccount;

use App\Domain\Localization\LocaleCode;
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

        #[Assert\Locale]
        #[Assert\Choice(callback: [LocaleCode::class, 'values'])]
        public readonly string $locale = LocaleCode::EnUs->value,
    ) {
    }
}
