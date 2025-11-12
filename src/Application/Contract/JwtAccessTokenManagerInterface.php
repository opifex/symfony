<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Domain\Foundation\AuthorizationToken;
use SensitiveParameter;

interface JwtAccessTokenManagerInterface
{
    /**
     * @param string[] $userRoles
     */
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string;

    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken;
}
