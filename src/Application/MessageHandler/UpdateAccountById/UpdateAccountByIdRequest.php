<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Entity\AccountRole;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateAccountByIdRequest
{
    public const string GROUP_EDITABLE = __CLASS__ . ':editable';

    /**
     * @param string[]|null $roles
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',

        #[Assert\Email]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?string $email = null,

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\PasswordStrength]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?string $password = null,

        #[Assert\Length(min: 5, max: 5)]
        #[Assert\Locale]
        #[Assert\Regex(pattern: '/^[a-z]{2}_[A-Z]{2}$/', message: 'This value is not a valid locale.')]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?string $locale = null,

        #[Assert\Choice(choices: AccountRole::ROLES, multiple: true)]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?array $roles = null,
    ) {
    }
}
