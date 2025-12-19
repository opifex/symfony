<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\SigninIntoAccount;

use App\Domain\Foundation\HttpSpecification;
use App\Domain\Foundation\MessageHandlerResult;

final class SigninIntoAccountCommandResult extends MessageHandlerResult
{
    public static function success(string $accessToken): self
    {
        return new self(
            data: [
                'access_token' => $accessToken,
            ],
            status: HttpSpecification::HTTP_OK,
        );
    }
}
