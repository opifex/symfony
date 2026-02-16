<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use App\Domain\Foundation\AbstractHandlerResult;

final class SigninIntoAccountCommandResult extends AbstractHandlerResult
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
