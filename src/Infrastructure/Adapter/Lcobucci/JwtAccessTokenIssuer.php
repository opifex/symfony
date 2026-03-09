<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

use App\Application\Contract\JwtAccessTokenIssuerInterface;
use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidConfigurationException;
use DateInterval;
use DateMalformedIntervalStringException;
use Lcobucci\JWT\Signer\InvalidKeyProvided;
use Override;
use Symfony\Component\Uid\Uuid;

final readonly class JwtAccessTokenIssuer implements JwtAccessTokenIssuerInterface
{
    public function __construct(
        private JwtConfigurationBag $jwtConfigurationBag,
    ) {
    }

    /**
     * @param non-empty-string $userIdentifier
     * @throws DateMalformedIntervalStringException
     */
    #[Override]
    public function issue(string $userIdentifier, array $userRoles = []): string
    {
        $config = $this->jwtConfigurationBag->create();
        $lifetimeInterval = new DateInterval(sprintf('PT%sS', $this->jwtConfigurationBag->lifetime));
        $tokenIssuedAt = $this->jwtConfigurationBag->clock->now();

        $builder = $config->builder()
            ->canOnlyBeUsedAfter($tokenIssuedAt)
            ->expiresAt($tokenIssuedAt->add($lifetimeInterval))
            ->identifiedBy(Uuid::v4()->toString())
            ->issuedAt($tokenIssuedAt)
            ->issuedBy($this->jwtConfigurationBag->issuer)
            ->relatedTo($userIdentifier)
            ->withClaim(name: JwtRegisteredClaims::ROLES, value: $userRoles);

        try {
            return $builder->getToken($config->signer(), $config->signingKey())->toString();
        } catch (InvalidKeyProvided $exception) {
            throw InvalidConfigurationException::tokenSignerIsNotConfigured($exception);
        }
    }
}
