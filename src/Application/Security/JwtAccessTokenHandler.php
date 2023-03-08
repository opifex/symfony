<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\Adapter\JwtTokenAdapterInterface;
use App\Domain\Exception\TokenAdapterException;
use SensitiveParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private JwtTokenAdapterInterface $jwtTokenAdapter)
    {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        try {
            return new UserBadge($this->jwtTokenAdapter->extractIdentifier($accessToken));
        } catch (TokenAdapterException $e) {
            throw new AccessDeniedHttpException($e->getMessage(), $e);
        }
    }
}
