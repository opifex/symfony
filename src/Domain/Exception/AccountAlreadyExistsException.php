<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: Response::HTTP_CONFLICT)]
class AccountAlreadyExistsException extends RuntimeException
{
}
