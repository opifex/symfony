<?php

declare(strict_types=1);

namespace App\Application\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_UNAUTHORIZED)]
class AuthorizationRequiredException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Invalid authorization credentials provided.');
    }
}
