<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteAccountByIdCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',
    ) {
    }
}
