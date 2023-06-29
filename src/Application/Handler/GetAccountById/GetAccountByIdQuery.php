<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountById;

use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountByIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',
    ) {
    }
}
