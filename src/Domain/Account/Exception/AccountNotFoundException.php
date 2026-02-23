<?php

declare(strict_types=1);

namespace App\Domain\Account\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(statusCode: 404)]
final class AccountNotFoundException extends RuntimeException
{
    public static function create(?Throwable $previous = null): self
    {
        return new self(message: 'Account with provided identifier not found.', previous: $previous);
    }
}
