<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\UpdateAccountById;

use App\Domain\Localization\LocaleCode;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateAccountByIdCommand
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
        #[Assert\Choice(callback: [LocaleCode::class, 'values'])]
        public readonly ?string $locale = null,
    ) {
    }
}
