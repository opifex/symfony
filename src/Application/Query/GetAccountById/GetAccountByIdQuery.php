<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountById;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetAccountByIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id = '',
    ) {
    }
}
