<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\PrivacyDataProtectorInterface;
use Override;

final readonly class RequestPrivacyDataProtector implements PrivacyDataProtectorInterface
{
    /** @var array<string, string> */
    private const array PATTERNS = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    #[Override]
    public function protect(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && array_key_exists($key, array: self::PATTERNS)) {
                $data[$key] = preg_replace(self::PATTERNS[$key], replacement: '*', subject: $value);
            } elseif (is_array($value)) {
                /** @var array<string, mixed> $value */
                $data[$key] = $this->protect($value);
            }
        }

        return $data;
    }
}
