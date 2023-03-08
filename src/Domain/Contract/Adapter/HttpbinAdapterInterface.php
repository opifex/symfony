<?php

declare(strict_types=1);

namespace App\Domain\Contract\Adapter;

interface HttpbinAdapterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getJson(): array;
}
