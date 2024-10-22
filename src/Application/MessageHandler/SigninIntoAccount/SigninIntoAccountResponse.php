<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

final class SigninIntoAccountResponse
{
    public readonly string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
