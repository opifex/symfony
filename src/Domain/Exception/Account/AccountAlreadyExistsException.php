<?php

declare(strict_types=1);

namespace App\Domain\Exception\Account;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_CONFLICT)]
class AccountAlreadyExistsException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Email address is already associated with another account.');
    }
}
