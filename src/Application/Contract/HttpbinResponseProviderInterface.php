<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\HttpbinRequestFailedException;

interface HttpbinResponseProviderInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinRequestFailedException
     */
    public function getJson(): array;
}
