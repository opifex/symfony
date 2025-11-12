<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface HttpbinResponseProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getJson(): array;
}
