<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Authenticator;

use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final readonly class JsonLoginAuthenticator implements InteractiveAuthenticatorInterface
{
    public function __construct(
        #[Autowire(service: 'limiter.json_login_authenticator')]
        private RateLimiterFactoryInterface $rateLimiterFactory,
    ) {
    }

    #[Override]
    public function authenticate(Request $request): Passport
    {
        $payload = $request->getPayload();
        $userBadge = new UserBadge($payload->getString(key: 'email'));
        $credentials = new PasswordCredentials($payload->getString(key: 'password'));

        return new Passport($userBadge, $credentials);
    }

    #[Override]
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new UsernamePasswordToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }

    #[Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $key = sha1($request->getPayload()->getString(key: 'email'));

        if (!$this->rateLimiterFactory->create($key)->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException(message: 'Too many requests detected, please try again later.');
        }

        return null;
    }

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    #[Override]
    public function supports(Request $request): bool
    {
        return $request->getContentTypeFormat() === 'json';
    }

    #[Override]
    public function isInteractive(): bool
    {
        return true;
    }
}
