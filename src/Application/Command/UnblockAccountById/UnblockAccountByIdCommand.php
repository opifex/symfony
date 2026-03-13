<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UnblockAccountByIdCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id = '',
    ) {
    }
}
