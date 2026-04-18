<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

use DateTimeImmutable;

final readonly class JwtAccessToken
{
    /**
     * @param string[] $userRoles
     */
    public function __construct(
        public string $identifier,
        public DateTimeImmutable $expiresAt,
        public string $userIdentifier,
        public array $userRoles,
    ) {
    }
}
