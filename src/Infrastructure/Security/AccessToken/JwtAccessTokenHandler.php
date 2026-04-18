<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AccessToken;

use App\Application\Contract\JwtAccessTokenRevokerInterface;
use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidTokenException;
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
        private JwtAccessTokenRevokerInterface $jwtAccessTokenRevoker,
    ) {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $authorizationToken = $this->jwtAccessTokenParser->parse($accessToken);

        if ($this->jwtAccessTokenRevoker->isRevoked($authorizationToken->identifier)) {
            throw InvalidTokenException::tokenIsAlreadyRevoked();
        }

        $userLoader = static fn(): TokenAuthenticatedUser => new TokenAuthenticatedUser(
            tokenIdentifier: $authorizationToken->identifier,
            tokenExpiresAt: $authorizationToken->expiresAt,
            userIdentifier: $authorizationToken->userIdentifier,
            userRoles: $authorizationToken->userRoles,
        );

        return new UserBadge($authorizationToken->userIdentifier, $userLoader);
    }
}
