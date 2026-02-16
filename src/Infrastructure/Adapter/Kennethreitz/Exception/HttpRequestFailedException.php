<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Kennethreitz\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(statusCode: 500)]
class HttpRequestFailedException extends RuntimeException
{
    public static function fromException(Throwable $previous): self
    {
        return new self(message: 'Httpbin responder encountered an exception.', previous: $previous);
    }
}
