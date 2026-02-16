<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use App\Domain\Foundation\MessageHandlerResult;

final class SigninIntoAccountCommandResult extends MessageHandlerResult
{
    public static function success(string $accessToken): self
    {
        return new self(
            payload: [
                'access_token' => $accessToken,
            ],
        );
    }
}
