<?php

declare(strict_types=1);

namespace App\Domain\Contract\Adapter;

use App\Domain\Exception\Adapter\HttpbinAdapterException;

interface HttpbinAdapterInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinAdapterException
     */
    public function getJson(): array;
}
