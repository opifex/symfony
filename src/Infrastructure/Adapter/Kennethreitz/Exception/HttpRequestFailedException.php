<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Kennethreitz\Exception;

use App\Domain\Foundation\HttpSpecification;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(statusCode: HttpSpecification::HTTP_INTERNAL_SERVER_ERROR)]
class HttpRequestFailedException extends RuntimeException
{
    public static function fromException(Throwable $previous): self
    {
        return new self(message: 'Httpbin responder encountered an exception.', previous: $previous);
    }
}
