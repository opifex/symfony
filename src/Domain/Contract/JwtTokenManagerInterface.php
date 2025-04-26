<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\JwtTokenManagerException;
use App\Domain\Model\AuthorizationToken;
use SensitiveParameter;

interface JwtTokenManagerInterface
{
    /**
     * @param string[] $userRoles
     * @throws JwtTokenManagerException
     */
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string;

    /**
     * @throws JwtTokenManagerException
     */
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken;
}
