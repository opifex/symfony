<?php

declare(strict_types=1);

namespace App\Application\Handler\DeleteAccountById;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: Response::HTTP_NO_CONTENT)]
final class DeleteAccountByIdResponse
{
}
