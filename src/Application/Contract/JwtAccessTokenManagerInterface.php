<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\JwtConfigurationFailedException;
use App\Application\Exception\JwtTokenInvalidException;
use App\Domain\Foundation\AuthorizationToken;
use SensitiveParameter;

interface JwtAccessTokenManagerInterface
{
    /**
     * @param string[] $userRoles
     * @throws JwtConfigurationFailedException
     */
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string;

    /**
     * @throws JwtTokenInvalidException
     */
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken;
}
