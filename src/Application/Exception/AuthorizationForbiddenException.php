<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_FORBIDDEN)]
class AuthorizationForbiddenException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'No privileges for the provided action.');
    }
}
