<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Protection\PrivacyDataProtectorInterface;
use Override;

final class RequestPrivacyDataProtector implements PrivacyDataProtectorInterface
{
    /** @var array<string, string> */
    private array $templates = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    #[Override]
    public function protect(array $data): array
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->templates) && is_string($value)) {
                $data[$key] = preg_replace($this->templates[$key], replacement: '*', subject: $value);
            } elseif (is_array($value)) {
                /** @var array<string, mixed> $value */
                $data[$key] = $this->protect($value);
            }
        }

        return $data;
    }
}
