<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountAction;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyAccountActionCommand implements MessageInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Groups(MessageInterface::GROUP_URL)]
        public readonly string $uuid = '',

        #[Assert\Choice(choices: AccountAction::LIST)]
        #[Assert\NotBlank]
        #[Groups(self::GROUP_URL)]
        public readonly string $action = '',
    ) {
    }
}
