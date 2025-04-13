<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\JwtTokenManagerInterface;
use App\Domain\Entity\AuthorizationToken;
use App\Domain\Exception\JwtTokenManagerException;
use DateInterval;
use Exception;
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

final class LcobucciJwtAdapter implements JwtTokenManagerInterface
{
    private const string CLAIM_ROLES = 'roles';

    private readonly Configuration $configuration;

    private readonly DateInterval $expiration;

    /**
     * @param non-empty-string $issuer
     * @throws Exception
     */
    public function __construct(
        #[Autowire('%env(string:JWT_ISSUER)%')]
        private readonly string $issuer,

        #[Autowire('%env(int:JWT_LIFETIME)%')]
        private readonly int $lifetime,

        #[Autowire('%env(string:JWT_PASSPHRASE)%')]
        #[SensitiveParameter]
        private readonly string $passphrase,

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
    public function decodeAccessToken(#[SensitiveParameter] string $accessToken): AuthorizationToken
    {
        try {
            $token = $this->parseTokenFromString($accessToken);
        } catch (CannotDecodeContent $e) {
            throw JwtTokenManagerException::errorWhileDecodingToken($e);
        } catch (InvalidTokenStructure $e) {
            throw JwtTokenManagerException::tokenHaveInvalidStructure($e);
        }

        $this->validateTokenInformation($token);

        /** @var string $userIdentifier */
        $userIdentifier = $token->claims()->get(name: RegisteredClaims::SUBJECT) ?? '';
        /** @var string[] $userRoles */
        $userRoles = $token->claims()->get(name: self::CLAIM_ROLES) ?? '';

        return new AuthorizationToken($userIdentifier, $userRoles);
    }

    /**
     * @param non-empty-string $userIdentifier
     */
    #[Override]
    public function createAccessToken(string $userIdentifier, array $userRoles = []): string
    {
        $tokenIssuedAt = $this->clock->now();
        $signer = $this->configuration->signer();
        $signingKey = $this->configuration->signingKey();

        $builder = $this->configuration->builder();
        $builder = $builder->canOnlyBeUsedAfter($tokenIssuedAt);
        $builder = $builder->expiresAt($tokenIssuedAt->add($this->expiration));
        $builder = $builder->identifiedBy($this->generateTokenIdentifier());
        $builder = $builder->issuedBy($this->issuer);
        $builder = $builder->issuedAt($tokenIssuedAt);
        $builder = $builder->relatedTo($userIdentifier);
        $builder = $builder->withClaim(name: self::CLAIM_ROLES, value: $userRoles);

        try {
            return $builder->getToken($signer, $signingKey)->toString();
        } catch (InvalidKeyProvided $e) {
            throw JwtTokenManagerException::tokenSignerIsNotConfigured($e);
        }
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
        $constraints = [
            new IssuedBy($this->issuer),
            new SignedWith($this->configuration->signer(), $this->configuration->verificationKey()),
            new StrictValidAt($this->clock),
        ];

        if (!$this->configuration->validator()->validate($token, ...$constraints)) {
            throw JwtTokenManagerException::tokenIsInvalidOrExpired();
        }
    }
}
