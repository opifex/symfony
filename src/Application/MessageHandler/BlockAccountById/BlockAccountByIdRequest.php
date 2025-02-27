<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\BlockAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
#[AsMessage]
final class BlockAccountByIdRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',
    ) {
    }
}
