<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UnblockAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class UnblockAccountByIdResponse
{
    public static function create(): self
    {
        return new self();
    }
}
