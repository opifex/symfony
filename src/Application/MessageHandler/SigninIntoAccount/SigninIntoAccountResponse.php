<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Entity\AuthorizationToken;

final class SigninIntoAccountResponse
{
    public readonly string $accessToken;

    public function __construct(AuthorizationToken $authorizationToken)
    {
        $this->accessToken = $authorizationToken->getSecret();
    }
}
