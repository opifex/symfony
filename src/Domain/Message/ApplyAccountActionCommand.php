<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\Contract\MessageInterface;
use App\Domain\Entity\AccountAction;
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