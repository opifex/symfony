<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
final class GetAccountByIdRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $uuid = '',
    ) {
    }
}
