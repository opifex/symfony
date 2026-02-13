<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use Symfony\Component\Validator\Constraints as Assert;

final class UnblockAccountByIdCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $id = '',
    ) {
    }
}
