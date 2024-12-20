<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Entity\AccountRole;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
final class CreateNewAccountRequest
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\NotBlank]
        #[Assert\PasswordStrength]
        public readonly string $password = '',

        #[Assert\Locale]
        #[Assert\Regex(pattern: '/^[a-z]{2}_[A-Z]{2}$/', message: 'This value is not a valid locale.')]
        public readonly string $locale = 'en_US',

        #[Assert\Choice(callback: [AccountRole::class, 'values'], multiple: true)]
        #[Assert\NotBlank]
        public readonly array $roles = [],
    ) {
    }
}
