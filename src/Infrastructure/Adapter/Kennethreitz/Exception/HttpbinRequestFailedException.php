<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Kennethreitz\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR)]
class HttpbinRequestFailedException extends RuntimeException
{
    public static function fromException(Throwable $previous): self
    {
        return new self(message: 'Httpbin responder encountered an exception.', previous: $previous);
    }
}
