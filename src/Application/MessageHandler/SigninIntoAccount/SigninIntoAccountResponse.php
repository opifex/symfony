<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class SigninIntoAccountResponse
{
    public function __construct(public readonly string $accessToken)
    {
    }

    public static function create(string $accessToken): self
    {
        return new self($accessToken);
    }
}
