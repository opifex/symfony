<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Contract\JwtTokenManagerInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private readonly JwtTokenManagerInterface $jwtTokenManager)
    {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        return new UserBadge($this->jwtTokenManager->extractUserIdentifier($accessToken));
    }
}
