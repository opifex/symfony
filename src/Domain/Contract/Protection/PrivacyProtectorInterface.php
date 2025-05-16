<?php

declare(strict_types=1);

namespace App\Domain\Contract\Protection;

interface PrivacyProtectorInterface
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function protect(array $data): array;
}
