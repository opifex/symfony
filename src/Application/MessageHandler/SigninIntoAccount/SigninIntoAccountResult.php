<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class SigninIntoAccountResult extends JsonResponse
{
    public static function success(string $accessToken): self
    {
        return new self(
            data: [
                'access_token' => $accessToken,
            ],
            status: Response::HTTP_OK,
        );
    }
}
