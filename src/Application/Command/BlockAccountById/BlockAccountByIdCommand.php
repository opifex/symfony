<?php

declare(strict_types=1);

namespace App\Application\Command\BlockAccountById;

use App\Application\Command\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class BlockAccountByIdCommand implements CommandInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id = '',
    ) {
    }
}
