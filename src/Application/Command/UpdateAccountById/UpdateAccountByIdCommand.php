<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use App\Domain\Localization\LocaleCode;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateAccountByIdCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id = '',

        #[Assert\Email]
        public ?string $email = null,

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\PasswordStrength]
        public ?string $password = null,

        #[Assert\Locale]
        #[Assert\Choice(callback: [LocaleCode::class, 'values'])]
        public ?string $locale = null,
    ) {
    }
}
