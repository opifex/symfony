<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface PrivacyDataProtectorInterface
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function protect(array $data): array;
}
