<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Authenticator;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: 429)]
final class AuthorizationThrottlingException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Too many requests detected, please try again later.');
    }
}
