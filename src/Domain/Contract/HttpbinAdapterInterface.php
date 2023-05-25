<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\HttpbinAdapterException;

interface HttpbinAdapterInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinAdapterException
     */
    public function getJson(): array;
}
