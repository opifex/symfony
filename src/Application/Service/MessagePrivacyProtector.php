<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\PrivacyProtectorInterface;
use Symfony\Component\String\UnicodeString;

final class MessagePrivacyProtector implements PrivacyProtectorInterface
{
    /** @var string[] */
    private array $templates = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    public function protect(array $data): array
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->templates) && is_string($value)) {
                $data[$key] = $this->replace($value, $this->templates[$key]);
            } elseif (is_array($value)) {
                $data[$key] = $this->protect($value);
            }
        }

        return $data;
    }

    private function replace(string $value, string $template): string
    {
        return (new UnicodeString($value))->replaceMatches($template, '*')->toString();
    }
}
