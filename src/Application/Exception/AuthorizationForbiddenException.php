<?php

declare(strict_types=1);

namespace App\Application\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_FORBIDDEN)]
class AuthorizationForbiddenException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'No privileges for the provided action.');
    }
}
