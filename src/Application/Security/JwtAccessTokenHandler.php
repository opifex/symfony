<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\JwtAdapterInterface;
use SensitiveParameter;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private JwtAdapterInterface $jwtAdapter)
    {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        return new UserBadge($this->jwtAdapter->extractIdentifier($accessToken));
    }
}
