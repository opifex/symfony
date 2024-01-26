<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\JwtAdapterInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class AuthorizationTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private ClockInterface $clock,
        private JwtAdapterInterface $jwtAdapter,
    ) {
    }

    #[Override]
    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        return new UserBadge($this->jwtAdapter->getIdentifier($accessToken, $this->clock));
    }
}
