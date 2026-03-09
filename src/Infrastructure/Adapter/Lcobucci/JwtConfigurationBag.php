<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidConfigurationException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use SensitiveParameter;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class JwtConfigurationBag
{
    /**
     * @param non-empty-string $issuer
     */
    public function __construct(
        #[Autowire(env: 'JWT_ISSUER')]
        public string $issuer,

        #[Autowire(env: 'JWT_LIFETIME')]
        public int $lifetime,

        #[Autowire(env: 'JWT_PASSPHRASE')]
        #[SensitiveParameter]
        public string $passphrase,

        #[Autowire(env: 'JWT_SIGNING_KEY')]
        #[SensitiveParameter]
        public ?string $signingKey = null,

        #[Autowire(env: 'JWT_VERIFICATION_KEY')]
        #[SensitiveParameter]
        public ?string $verificationKey = null,

        public ClockInterface $clock = new Clock(),
    ) {
    }

    public function create(): Configuration
    {
        if ($this->passphrase === '' || $this->signingKey === '' || $this->verificationKey === '') {
            throw InvalidConfigurationException::tokenSignerIsNotConfigured();
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
