<?php

declare(strict_types=1);

namespace App\Domain\Exception\Authorization;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNAUTHORIZED)]
class AuthorizationRequiredException extends RuntimeException
{
    public static function create(): self
    {
        return new self(message: 'Authorization required to perform this action.');
    }
}
