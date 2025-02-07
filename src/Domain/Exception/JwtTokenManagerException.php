<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_FORBIDDEN)]
class JwtTokenManagerException extends RuntimeException
{
    public static function tokenSignerIsNotConfigured(): self
    {
        return new self(message: 'Authorization token signer is not configured.');
    }

    public static function errorWhileDecodingToken(): self
    {
        return new self(message: 'Error while decoding authorization token.');
    }

    public static function tokenHaveInvalidStructure(): self
    {
        return new self(message: 'Authorization token have invalid structure.');
    }

    public static function tokenIsInvalidOrExpired(): self
    {
        return new self(message: 'Authorization token is invalid or expired.');
    }
}
