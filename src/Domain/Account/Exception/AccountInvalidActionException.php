<?php

declare(strict_types=1);

namespace App\Domain\Account\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_UNPROCESSABLE_ENTITY)]
class AccountInvalidActionException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Provided action cannot be applied to account.');
    }
}
