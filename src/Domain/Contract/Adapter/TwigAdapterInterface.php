<?php

declare(strict_types=1);

namespace App\Domain\Contract\Adapter;

use App\Domain\Exception\Adapter\TwigAdapterException;

interface TwigAdapterInterface
{
    /**
     * @param string $name
     * @param array&array<string, mixed> $context
     * @throws TwigAdapterException
     */
    public function render(string $name, array $context = []): string;
}
