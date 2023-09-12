<?php

declare(strict_types=1);

namespace App\Application\Handler\ApplyAccountAction;

use App\Domain\Entity\AccountAction;
use Symfony\Component\Validator\Constraints as Assert;

final class ApplyAccountActionCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',

        #[Assert\Choice(choices: AccountAction::ACTIONS)]
        #[Assert\NotBlank]
        public readonly string $action = '',
    ) {
    }
}
