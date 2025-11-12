<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_FORBIDDEN)]
class JwtTokenInvalidException extends RuntimeException
{
    public static function errorWhileDecodingToken(?Throwable $previous = null): self
    {
        return new self(message: 'Error while decoding authorization token.', previous: $previous);
    }

    public static function tokenHaveInvalidStructure(?Throwable $previous = null): self
    {
        return new self(message: 'Authorization token have invalid structure.', previous: $previous);
    }

    public static function tokenIsInvalidOrExpired(?Throwable $previous = null): self
    {
        return new self(message: 'Authorization token is invalid or expired.', previous: $previous);
    }
}
