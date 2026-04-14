<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

use DomainException;

final readonly class PasswordHash
{
    private const string BCRYPT_PATTERN = '/^\$2[ayb]\$\d{2}\$.{53}$/';
    private const string ARGON2_PATTERN = '/^\$argon2(i|d|id)\$v=\d+\$m=\d+,t=\d+,p=\d+\$.+\$.+$/';

    private function __construct(
        private string $passwordHash,
    ) {
    }

    public static function fromString(string $passwordHash): self
    {
        $passwordHash = trim($passwordHash);

        $isBcrypt = preg_match(pattern: self::BCRYPT_PATTERN, subject: $passwordHash) === 1;
        $isArgon2 = preg_match(pattern: self::ARGON2_PATTERN, subject: $passwordHash) === 1;

        if (!$isBcrypt && !$isArgon2) {
            throw new DomainException(message: 'Invalid password hash format provided.');
        }

        return new self($passwordHash);
    }

    public function toString(): string
    {
        return $this->passwordHash;
    }
}
