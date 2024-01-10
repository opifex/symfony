<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\JwtAdapterInterface;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class PasswordAuthenticator implements InteractiveAuthenticatorInterface
{
    public function __construct(private JwtAdapterInterface $jwtAdapter)
    {
    }

    #[Override]
    public function authenticate(Request $request): Passport
    {
        $credentials = $request->getPayload();

        $email = $credentials->getString(key: 'email');
        $password = $credentials->getString(key: 'password');

        return new Passport(new UserBadge($email), new PasswordCredentials($password));
    }

    #[Override]
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        $secret = $this->jwtAdapter->createToken($passport->getUser());

        return new JwtAccessToken($passport->getUser(), $firewallName, $secret);
    }

    #[Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    #[Override]
    public function supports(Request $request): ?bool
    {
        return $request->getContentTypeFormat() === 'json';
    }

    #[Override]
    public function isInteractive(): bool
    {
        return true;
    }
}
