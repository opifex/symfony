<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountRole;
use App\Domain\Message\MessageUuidTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateAccountByIdCommand implements MessageInterface
{
    use MessageUuidTrait;

    #[Assert\Email]
    #[Groups(self::GROUP_BODY)]
    public ?string $email = null;

    #[Assert\Length(min: 8, max: 32)]
    #[Assert\NotCompromisedPassword]
    #[Groups(self::GROUP_BODY)]
    public ?string $password = null;

    /**
     * @var string[]
     */
    #[Assert\Choice(choices: AccountRole::LIST, multiple: true)]
    #[Groups(self::GROUP_BODY)]
    public ?array $roles = null;
}
