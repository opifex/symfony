<?php

declare(strict_types=1);

namespace App\Domain\Exception\Account;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
class AccountActionInvalidException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Provided action cannot be applied to account.');
    }
}
