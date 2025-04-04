<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class UpdateAccountByIdResponse extends JsonResponse
{
    public static function create(): self
    {
        return new self(
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
