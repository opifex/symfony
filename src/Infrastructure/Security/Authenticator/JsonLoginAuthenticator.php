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
        #[Autowire(service: 'limiter.email_ip_login')]
        private RateLimiterFactoryInterface $emailIpRateLimiterFactory,
        #[Autowire(service: 'limiter.ip_login')]
        private RateLimiterFactoryInterface $ipRateLimiterFactory,
    ) {
    }

    #[Override]
    public function authenticate(Request $request): Passport
    {
        $payload = $request->getPayload();
        $userBadge = new UserBadge($payload->getString(key: 'email'));
        $credentials = new PasswordCredentials($payload->getString(key: 'password'));

        $this->enforceRateLimit($userBadge->getUserIdentifier(), $request->getClientIp(), tokens: 0);

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
        $email = $request->getPayload()->getString(key: 'email');

        $this->enforceRateLimit($email, $request->getClientIp());

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

    private function enforceRateLimit(string $email, ?string $ip = null, int $tokens = 1): void
    {
        $normalizedEmail = mb_strtolower(trim($email), 'UTF-8');
        $normalizedIp = mb_strtolower(trim(string: $ip ?? 'unknown'), 'UTF-8');

        $emailIpKey = hash(algo: 'sha256', data: $normalizedEmail . '|' . $normalizedIp);
        $ipKey = hash(algo: 'sha256', data: $normalizedIp);

        $emailIpLimit = $this->emailIpRateLimiterFactory->create($emailIpKey)->consume($tokens);
        $ipLimit = $this->ipRateLimiterFactory->create($ipKey)->consume($tokens);

        if (!$emailIpLimit->isAccepted() || !$ipLimit->isAccepted()) {
            throw new TooManyRequestsHttpException(message: 'Too many requests detected, please try again later.');
        }
    }
}
