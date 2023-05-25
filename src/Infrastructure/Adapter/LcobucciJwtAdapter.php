<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\JwtAdapterInterface;
use App\Domain\Exception\JwtAdapterException;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Lcobucci\Clock\SystemClock;
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
use SensitiveParameter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV4;

final class LcobucciJwtAdapter implements JwtAdapterInterface
{
    private readonly Configuration $configuration;

    private readonly DateInterval $expiration;

    /**
     * @throws Exception
     */
    public function __construct(
        int $lifetime,
        #[SensitiveParameter] string $passphrase,
        #[SensitiveParameter] ?string $signingKey = null,
        #[SensitiveParameter] ?string $verificationKey = null,
    ) {
        $this->expiration = $this->buildExpiration($lifetime);
        $this->configuration = $this->buildConfiguration($passphrase, $signingKey, $verificationKey);
    }

    public function extractIdentifier(#[SensitiveParameter] string $accessToken): string
    {
        try {
            $accessToken = $this->configuration->parser()->parse($accessToken);
        } catch (CannotDecodeContent) {
            throw new JwtAdapterException(message: 'Error while decoding authorization token.');
        } catch (InvalidTokenStructure) {
            throw new JwtAdapterException(message: 'Authorization token have invalid structure.');
        }

        $this->validateTokenConstraints($accessToken);

        return $this->extractSubjectFromToken($accessToken);
    }

    /**
     * @throws Exception
     */
    public function generateToken(UserInterface $user): string
    {
        $time = new DateTimeImmutable();

        return $this->configuration->builder()
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time->add($this->expiration))
            ->identifiedBy((new UuidV4())->toRfc4122())
            ->issuedAt($time)
            ->relatedTo($user->getUserIdentifier())
            ->getToken($this->configuration->signer(), $this->configuration->signingKey())
            ->toString();
    }

    /**
     * @throws JwtAdapterException
     */
    private function buildConfiguration(
        #[SensitiveParameter] string $passphrase,
        #[SensitiveParameter] ?string $signingKey = null,
        #[SensitiveParameter] ?string $verificationKey = null,
    ): Configuration {
        if (!empty($passphrase) && !empty($signingKey) && !empty($verificationKey)) {
            $configuration = Configuration::forAsymmetricSigner(
                signer: new RsaSha256(),
                signingKey: InMemory::plainText($signingKey, $passphrase),
                verificationKey: InMemory::plainText($verificationKey, $passphrase),
            );
        } elseif (!empty($passphrase) && empty($signingKey) && empty($verificationKey)) {
            $configuration = Configuration::forSymmetricSigner(
                signer: new HmacSha256(),
                key: InMemory::plainText($passphrase),
            );
        }

        return $configuration ?? throw new JwtAdapterException(
            message: 'Authorization token signer is not configured.',
        );
    }

    /**
     * @throws Exception
     */
    private function buildExpiration(int $lifetime): DateInterval
    {
        return new DateInterval(sprintf('PT%sS', $lifetime));
    }

    /**
     * @throws JwtAdapterException
     */
    private function extractSubjectFromToken(Token $token): string
    {
        if (method_exists($token, method: 'claims')) {
            $subject = $token->claims()->get(name: RegisteredClaims::SUBJECT);
        }

        if (!isset($subject) || !is_string($subject)) {
            throw new JwtAdapterException(message: 'Authorization token is invalid or expired.');
        }

        return $subject;
    }

    /**
     * @throws JwtAdapterException
     */
    private function validateTokenConstraints(Token $token): void
    {
        $strictValidAt = new StrictValidAt(SystemClock::fromSystemTimezone(), $this->expiration);
        $signedWith = new SignedWith($this->configuration->signer(), $this->configuration->verificationKey());

        if (!$this->configuration->validator()->validate($token, $strictValidAt, $signedWith)) {
            throw new JwtAdapterException(message: 'Authorization token is invalid or expired.');
        }
    }
}
