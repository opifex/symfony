<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_INTERNAL_SERVER_ERROR)]
class InvalidConfigurationException extends RuntimeException
{
    public static function tokenSignerIsNotConfigured(?Throwable $previous = null): self
    {
        return new self(message: 'Authorization token signer is not configured.', previous: $previous);
    }
}
