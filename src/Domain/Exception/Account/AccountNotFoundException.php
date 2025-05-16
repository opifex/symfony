<?php

declare(strict_types=1);

namespace App\Domain\Exception\Account;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_NOT_FOUND)]
class AccountNotFoundException extends RuntimeException
{
    public static function create(?Throwable $previous = null): self
    {
        return new self(message: 'Account with provided identifier not found.', previous: $previous);
    }
}
