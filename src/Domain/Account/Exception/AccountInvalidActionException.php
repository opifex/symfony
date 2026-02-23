<?php

declare(strict_types=1);

namespace App\Domain\Account\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: 422)]
final class AccountInvalidActionException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Provided action cannot be applied to account.');
    }
}
