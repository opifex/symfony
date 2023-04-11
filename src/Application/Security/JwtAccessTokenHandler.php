<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\Adapter\JwtAdapterInterface;
use App\Domain\Exception\Adapter\JwtAdapterException;
use SensitiveParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private JwtAdapterInterface $jwtAdapter)
    {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        try {
            return new UserBadge($this->jwtAdapter->extractIdentifier($accessToken));
        } catch (JwtAdapterException $e) {
            throw new AccessDeniedHttpException($e->getMessage(), $e);
        }
    }
}
