<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AccessToken;

use App\Infrastructure\Adapter\Lcobucci\JwtAccessTokenParser;
use App\Infrastructure\Security\AuthenticatedUser\TokenAuthenticatedUser;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final readonly class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private JwtAccessTokenParser $jwtAccessTokenParser,
    ) {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $authorizationToken = $this->jwtAccessTokenParser->parse($accessToken);

        $userLoader = static fn(): TokenAuthenticatedUser => new TokenAuthenticatedUser(
            userIdentifier: $authorizationToken->userIdentifier,
            roles: $authorizationToken->userRoles,
        );

        return new UserBadge($authorizationToken->userIdentifier, $userLoader);
    }
}
