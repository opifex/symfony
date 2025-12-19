<?php

declare(strict_types=1);

namespace App\Application\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_TOO_MANY_REQUESTS)]
class AuthorizationThrottlingException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Too many requests detected, please try again later.');
    }
}
