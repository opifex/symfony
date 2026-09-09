<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

final readonly class SensitiveDataProtector
{
    /**
     * @param array<array-key, mixed> $data
     * @param array<string, string> $patterns
     * @return array<array-key, mixed>
     */
    public function protect(array $data, array $patterns): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && array_key_exists($key, array: $patterns)) {
                $data[$key] = preg_replace($patterns[$key], replacement: '*', subject: $value);
            } elseif (is_array($value)) {
                /** @var array<array-key, mixed> $value */
                $data[$key] = $this->protect($value, $patterns);
            }
        }

        return $data;
    }
}
