<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class DeleteAccountByIdResult extends JsonResponse
{
    public static function success(): self
    {
        return new self(
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
