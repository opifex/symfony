<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\JwtTokenManagerInterface;
use App\Domain\Exception\JwtTokenManagerException;
use DateInterval;
use Exception;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Override;
use SensitiveParameter;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final class LcobucciJwtAdapter implements JwtTokenManagerInterface
{
    private readonly ClockInterface $clock;

    private readonly Configuration $configuration;

    private readonly DateInterval $expiration;

    /**
     * @throws Exception
     */
    public function __construct(
        int $lifetime = 0,
        ClockInterface $clock = new Clock(),
        #[SensitiveParameter]
        ?string $passphrase = null,
        #[SensitiveParameter]
        ?string $signingKey = null,
        #[SensitiveParameter]
        ?string $verificationKey = null,
    ) {
        $this->expiration = new DateInterval(sprintf('PT%sS', $lifetime));
        $this->clock = $clock;

        $this->configuration = match (true) {
            !empty($passphrase) && empty($signingKey) && empty($verificationKey) =>
            Configuration::forSymmetricSigner(
                signer: new HmacSha256(),
                key: InMemory::plainText($passphrase),
            ),
            !empty($passphrase) && !empty($signingKey) && !empty($verificationKey) =>
            Configuration::forAsymmetricSigner(
                signer: new RsaSha256(),
                signingKey: InMemory::plainText($signingKey, $passphrase),
                verificationKey: InMemory::plainText($verificationKey, $passphrase),
            ),
            default => throw new JwtTokenManagerException(
                message: 'Authorization token signer is not configured.',
            ),
        };
    }

    #[Override]
    public function extractUserIdentifier(#[SensitiveParameter] string $accessToken): string
    {
        try {
            $token = $this->configuration->parser()->parse($accessToken);
        } catch (CannotDecodeContent) {
            throw new JwtTokenManagerException(message: 'Error while decoding authorization token.');
        } catch (InvalidTokenStructure) {
            throw new JwtTokenManagerException(message: 'Authorization token have invalid structure.');
        }

        $strictValidAt = new StrictValidAt(new FrozenClock($this->clock->now()));
        $signedWith = new SignedWith($this->configuration->signer(), $this->configuration->verificationKey());

        if (!$this->configuration->validator()->validate($token, $strictValidAt, $signedWith)) {
            throw new JwtTokenManagerException(message: 'Authorization token is invalid or expired.');
        }

        return $this->extractSubjectFromToken($token);
    }

    #[Override]
    public function generateToken(string $userIdentifier): string
    {
        $tokenIssuedAt = $this->clock->now();
        $tokenExpiresAt = $tokenIssuedAt->add($this->expiration);

        $builder = $this->configuration->builder();
        $builder->canOnlyBeUsedAfter($tokenIssuedAt);
        $builder->expiresAt($tokenExpiresAt);
        $builder->identifiedBy(Uuid::v4()->toRfc4122());
        $builder->issuedAt($tokenIssuedAt);
        $builder->relatedTo($userIdentifier);

        $token = $builder->getToken(
            signer: $this->configuration->signer(),
            key: $this->configuration->signingKey(),
        );

        return $token->toString();
    }

    /**
     * @throws JwtTokenManagerException
     */
    private function extractSubjectFromToken(Token $token): string
    {
        if (method_exists($token, method: 'claims')) {
            $subject = $token->claims()->get(name: RegisteredClaims::SUBJECT);
        }

        if (!isset($subject) || !is_string($subject)) {
            throw new JwtTokenManagerException(message: 'Authorization token is invalid or expired.');
        }

        return $subject;
    }
}
