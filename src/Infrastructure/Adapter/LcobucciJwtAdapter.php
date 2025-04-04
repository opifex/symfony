<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\JwtTokenManagerInterface;
use App\Domain\Exception\JwtTokenManagerException;
use DateInterval;
use Exception;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Override;
use SensitiveParameter;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

final class LcobucciJwtAdapter implements JwtTokenManagerInterface
{
    private readonly Configuration $configuration;

    private readonly DateInterval $expiration;

    /**
     * @throws Exception
     */
    public function __construct(
        #[Autowire('%env(int:JWT_LIFETIME)%')]
        private readonly int $lifetime = 0,

        #[Autowire('%env(JWT_PASSPHRASE)%')]
        #[SensitiveParameter]
        private readonly ?string $passphrase = null,

        #[Autowire('%env(default::JWT_SIGNING_KEY)%')]
        #[SensitiveParameter]
        private readonly ?string $signingKey = null,

        #[Autowire('%env(default::JWT_VERIFICATION_KEY)%')]
        #[SensitiveParameter]
        private readonly ?string $verificationKey = null,

        private readonly ClockInterface $clock = new Clock(),
    ) {
        $this->expiration = new DateInterval(sprintf('PT%sS', $this->lifetime));
        $this->configuration = match (true) {
            !empty($this->passphrase) && empty($this->signingKey) && empty($this->verificationKey) =>
            Configuration::forSymmetricSigner(
                signer: new HmacSha256(),
                key: InMemory::plainText($this->passphrase),
            ),
            !empty($this->passphrase) && !empty($this->signingKey) && !empty($this->verificationKey) =>
            Configuration::forAsymmetricSigner(
                signer: new RsaSha256(),
                signingKey: InMemory::plainText($this->signingKey, $this->passphrase),
                verificationKey: InMemory::plainText($this->verificationKey, $this->passphrase),
            ),
            default => throw JwtTokenManagerException::tokenSignerIsNotConfigured(),
        };
    }

    /**
     * @param non-empty-string $accessToken
     */
    #[Override]
    public function extractUserIdentifier(#[SensitiveParameter] string $accessToken): string
    {
        try {
            $token = $this->parseTokenFromString($accessToken);
        } catch (CannotDecodeContent) {
            throw JwtTokenManagerException::errorWhileDecodingToken();
        } catch (InvalidTokenStructure) {
            throw JwtTokenManagerException::tokenHaveInvalidStructure();
        }

        $this->validateTokenInformation($token);

        return $this->extractSubjectFromToken($token);
    }

    /**
     * @param non-empty-string $userIdentifier
     */
    #[Override]
    public function generateToken(string $userIdentifier): string
    {
        $tokenIssuedAt = $this->clock->now();
        $tokenExpiresAt = $tokenIssuedAt->add($this->expiration);
        $tokenIdentifier = $this->generateTokenIdentifier();

        $builder = $this->configuration->builder();
        $builder = $builder->canOnlyBeUsedAfter($tokenIssuedAt);
        $builder = $builder->expiresAt($tokenExpiresAt);
        $builder = $builder->identifiedBy($tokenIdentifier);
        $builder = $builder->issuedAt($tokenIssuedAt);
        $builder = $builder->relatedTo($userIdentifier);

        $token = $builder->getToken(
            signer: $this->configuration->signer(),
            key: $this->configuration->signingKey(),
        );

        return $token->toString();
    }

    /**
     * @return non-empty-string
     */
    private function generateTokenIdentifier(): string
    {
        /** @var non-empty-string */
        return Uuid::v4()->hash();
    }

    /**
     * @param non-empty-string $accessToken
     */
    private function parseTokenFromString(string $accessToken): Plain
    {
        /** @var Plain */
        return $this->configuration->parser()->parse($accessToken);
    }

    private function validateTokenInformation(Plain $token): void
    {
        $strictValidAt = new StrictValidAt($this->clock);
        $signedWith = new SignedWith($this->configuration->signer(), $this->configuration->verificationKey());

        if (!$this->configuration->validator()->validate($token, $strictValidAt, $signedWith)) {
            throw JwtTokenManagerException::tokenIsInvalidOrExpired();
        }
    }

    /**
     * @throws JwtTokenManagerException
     */
    private function extractSubjectFromToken(Plain $token): string
    {
        $subject = $token->claims()->get(name: RegisteredClaims::SUBJECT);

        if (!is_string($subject)) {
            throw JwtTokenManagerException::tokenIsInvalidOrExpired();
        }

        return $subject;
    }
}
