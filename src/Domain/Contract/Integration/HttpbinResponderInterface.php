<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\HttpbinResponderException;

interface HttpbinResponderInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinResponderException
     */
    public function getJson(): array;
}
