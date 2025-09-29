<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Contract\JwtAccessTokenManagerInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly JwtAccessTokenManagerInterface $jwtAccessTokenManager,
    ) {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $authorizationToken = $this->jwtAccessTokenManager->decodeAccessToken($accessToken);
        $userLoader = static fn(): TokenAuthenticatedUser => new TokenAuthenticatedUser(
            userIdentifier: $authorizationToken->getUserIdentifier(),
            roles: $authorizationToken->getUserRoles(),
        );

        return new UserBadge($authorizationToken->getUserIdentifier(), $userLoader);
    }
}
