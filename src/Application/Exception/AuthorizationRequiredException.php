<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: 401)]
final class AuthorizationRequiredException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Invalid authorization credentials provided.');
    }
}
