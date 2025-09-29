<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR)]
class JwtConfigurationFailedException extends RuntimeException
{
    public static function tokenSignerIsNotConfigured(?Throwable $previous = null): self
    {
        return new self(message: 'Authorization token signer is not configured.', previous: $previous);
    }
}
