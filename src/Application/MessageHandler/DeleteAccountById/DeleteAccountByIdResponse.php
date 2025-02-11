<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class DeleteAccountByIdResponse
{
    public static function create(): self
    {
        return new self();
    }
}
