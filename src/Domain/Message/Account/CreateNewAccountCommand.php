<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountRole;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateNewAccountCommand implements MessageInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[Groups(self::GROUP_BODY)]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        #[Groups(self::GROUP_BODY)]
        public readonly string $password = '',

        #[Assert\Choice(choices: AccountRole::LIST, multiple: true)]
        #[Assert\NotBlank]
        #[Groups(self::GROUP_BODY)]
        public readonly array $roles = [],
    ) {
    }
}
