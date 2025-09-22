<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\HttpbinResponseProviderException;

interface HttpbinResponseProviderInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinResponseProviderException
     */
    public function getJson(): array;
}
