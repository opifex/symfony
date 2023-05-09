<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountAction;
use Symfony\Component\Validator\Constraints as Assert;

final class ApplyAccountActionCommand implements MessageInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',

        #[Assert\Choice(choices: AccountAction::LIST)]
        #[Assert\NotBlank]
        public readonly string $action = '',
    ) {
    }
}
