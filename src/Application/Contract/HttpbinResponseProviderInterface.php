<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface HttpbinResponseProviderInterface
{
    /**
     * @return array<array-key, mixed>
     */
    public function getJson(): array;
}
