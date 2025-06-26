<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\JwtAccessTokenManagerException;
use App\Domain\Model\AuthorizationToken;
use SensitiveParameter;

interface JwtAccessTokenManagerInterface
{
    /**
     * @param string[] $userRoles
     * @throws JwtAccessTokenManagerException
     */
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string;

    /**
     * @throws JwtAccessTokenManagerException
     */
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken;
}
