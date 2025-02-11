<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class UpdateAccountByIdResponse
{
    public static function create(): self
    {
        return new self();
    }
}
