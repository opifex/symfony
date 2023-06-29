<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountDetails
{
    public function __construct(public readonly string $email)
    {
    }
}
