<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class SignupNewAccountResponse
{
    public static function create(): self
    {
        return new self();
    }
}
