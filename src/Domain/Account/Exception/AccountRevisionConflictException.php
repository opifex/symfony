<?php

declare(strict_types=1);

namespace App\Domain\Account\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: 409)]
final class AccountRevisionConflictException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Account revision conflict detected. Please retry.');
    }
}
