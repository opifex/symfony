<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class CreateNewAccountResult extends JsonResponse
{
    public static function success(string $uuid): self
    {
        return new self(
            data: [
                'uuid' => $uuid,
            ],
            status: Response::HTTP_CREATED,
        );
    }
}
