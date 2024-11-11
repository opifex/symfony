<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\ApplyAccountAction;

use App\Domain\Entity\AccountAction;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
final class ApplyAccountActionRequest
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
