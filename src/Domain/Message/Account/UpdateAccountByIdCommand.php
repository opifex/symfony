<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountRole;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateAccountByIdCommand implements MessageInterface
{
    public const GROUP_EDITABLE = __CLASS__ . ':editable';

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
        #[Assert\NotCompromisedPassword]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?string $password = null,

        #[Assert\Length(min: 2, max: 2)]
        #[Assert\Locale]
        #[Assert\Regex(pattern: '/^[a-z]+$/', message: 'This value should have only lowercase letters.')]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?string $locale = null,

        #[Assert\Choice(choices: AccountRole::LIST, multiple: true)]
        #[Groups(self::GROUP_EDITABLE)]
        public readonly ?array $roles = null,
    ) {
    }
}
