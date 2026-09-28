<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountById;

use App\Application\Query\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetAccountByIdQuery implements QueryInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id = '',
    ) {
    }
}
