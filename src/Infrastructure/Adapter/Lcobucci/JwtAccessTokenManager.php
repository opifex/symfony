<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

use App\Application\Contract\JwtAccessTokenManagerInterface;
use App\Domain\Foundation\AuthorizationToken;
use App\Infrastructure\Adapter\Lcobucci\Exception\JwtConfigurationFailedException;
use App\Infrastructure\Adapter\Lcobucci\Exception\JwtTokenInvalidException;
use DateInterval;
use DateMalformedIntervalStringException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\InvalidKeyProvided;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Override;
use SensitiveParameter;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

final class JwtAccessTokenManager implements JwtAccessTokenManagerInterface
{
    private const string CLAIM_ROLES = 'roles';

    /**
     * @param non-empty-string $issuer
     */
    public function __construct(
        #[Autowire('%env(string:JWT_ISSUER)%')]
        private readonly string $issuer,

        #[Autowire('%env(int:JWT_LIFETIME)%')]
        private readonly int $lifetime,

        #[Autowire('%env(string:JWT_PASSPHRASE)%')]
        #[SensitiveParameter]
        private readonly string $passphrase,

        #[Autowire('%env(default::string:JWT_SIGNING_KEY)%')]
        #[SensitiveParameter]
        private readonly ?string $signingKey = null,

        #[Autowire('%env(default::string:JWT_VERIFICATION_KEY)%')]
        #[SensitiveParameter]
        private readonly ?string $verificationKey = null,

        private readonly ClockInterface $clock = new Clock(),
    ) {
    }

    /**
     * @param non-empty-string $accessToken
     */
    #[Override]
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken
    {
        $jsonWebToken = $this->getJwtConfiguration();

        try {
            /** @var Plain $token */
            $token = $jsonWebToken->parser()->parse($accessToken);
        } catch (CannotDecodeContent $e) {
            throw JwtTokenInvalidException::errorWhileDecodingToken($e);
        } catch (InvalidTokenStructure $e) {
            throw JwtTokenInvalidException::tokenHaveInvalidStructure($e);
        }

        if (!$jsonWebToken->validator()->validate($token, ...$jsonWebToken->validationConstraints())) {
            throw JwtTokenInvalidException::tokenIsInvalidOrExpired();
        }

        return $this->getAuthorizationToken($token);
    }

    /**
     * @param non-empty-string $userIdentifier
     * @throws DateMalformedIntervalStringException
     */
    #[Override]
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string
    {
        $jsonWebToken = $this->getJwtConfiguration();
        $tokenIssuedAt = $this->clock->now();

        $builder = $jsonWebToken->builder()
            ->canOnlyBeUsedAfter($tokenIssuedAt)
            ->expiresAt($tokenIssuedAt->add($this->getLifetimeInterval()))
            ->identifiedBy($this->getTokenIdentifier())
            ->issuedAt($tokenIssuedAt)
            ->issuedBy($this->issuer)
            ->relatedTo($userIdentifier)
            ->withClaim(name: self::CLAIM_ROLES, value: $userRoles);

        try {
            return $builder->getToken($jsonWebToken->signer(), $jsonWebToken->signingKey())->toString();
        } catch (InvalidKeyProvided $e) {
            throw JwtConfigurationFailedException::tokenSignerIsNotConfigured($e);
        }
    }

    /**
     * @return non-empty-string
     */
    private function getTokenIdentifier(): string
    {
        /** @var non-empty-string */
        return Uuid::v4()->toString();
    }

    /**
     * @throws DateMalformedIntervalStringException
     */
    private function getLifetimeInterval(): DateInterval
    {
        return new DateInterval(sprintf('PT%sS', $this->lifetime));
    }

    private function getAuthorizationToken(Plain $token): AuthorizationToken
    {
        /** @var string $userIdentifier */
        $userIdentifier = $token->claims()->get(name: RegisteredClaims::SUBJECT) ?? '';
        /** @var string[] $userRoles */
        $userRoles = $token->claims()->get(name: self::CLAIM_ROLES) ?? [];

        return new AuthorizationToken($userIdentifier, $userRoles);
    }

    private function getJwtConfiguration(): Configuration
    {
        if ($this->passphrase === '' || $this->signingKey === '' || $this->verificationKey === '') {
            throw JwtConfigurationFailedException::tokenSignerIsNotConfigured();
        }

        $config = match (true) {
            $this->signingKey !== null && $this->verificationKey !== null => Configuration::forAsymmetricSigner(
                signer: new RsaSha256(),
                signingKey: InMemory::plainText($this->signingKey, $this->passphrase),
                verificationKey: InMemory::plainText($this->verificationKey, $this->passphrase),
            ),
            default => Configuration::forSymmetricSigner(
                signer: new HmacSha256(),
                key: InMemory::plainText($this->passphrase),
            ),
        };

        return $config->withValidationConstraints(
            new IssuedBy($this->issuer),
            new SignedWith($config->signer(), $config->verificationKey()),
            new StrictValidAt($this->clock),
        );
    }
}
