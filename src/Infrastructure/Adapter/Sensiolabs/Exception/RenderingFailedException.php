<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Sensiolabs\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(statusCode: 500)]
final class RenderingFailedException extends RuntimeException
{
    public static function fromException(Throwable $previous): self
    {
        return new self(message: 'Template renderer encountered an exception.', previous: $previous);
    }
}
