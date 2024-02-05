<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\HttpbinResponderException;

interface HttpbinResponderInterface
{
    /**
     * @return array<string, mixed>
     * @throws HttpbinResponderException
     */
    public function getJson(): array;
}
