<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\HttpbinAdapterException;

interface CoreAnalyzerInterface
{
    public function cache(string $key, string $value): string;

    /**
     * @return array<string, mixed>
     * @throws HttpbinAdapterException
     */
    public function httpbin(): array;
}
