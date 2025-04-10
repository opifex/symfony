<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\AuthorizationToken;
use App\Domain\Exception\JwtTokenManagerException;
use SensitiveParameter;

interface JwtTokenManagerInterface
{
    /**
     * @throws JwtTokenManagerException
     */
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken;

    public function createAccessToken(string $userIdentifier): string;
}
