<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\Adapter\JwtAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class PasswordAuthenticator implements AuthenticatorInterface
{
    public function __construct(private JwtAdapterInterface $jwtAdapter)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->query->get(key: 'email');
        $password = $request->query->get(key: 'password');

        if (!is_string($email) || !is_string($password)) {
            throw new BadCredentialsException();
        }

        return new Passport(new UserBadge($email), new PasswordCredentials($password));
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        $user = $passport->getUser();
        $token = $this->jwtAdapter->generateToken($user);

        return new JwtAccessToken($user, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function supports(Request $request): ?bool
    {
        return $request->query->has(key: 'email') && $request->query->has(key: 'password');
    }
}
