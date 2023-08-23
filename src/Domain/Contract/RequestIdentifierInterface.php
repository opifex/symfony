<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use Symfony\Component\HttpFoundation\Request;

interface RequestIdentifierInterface
{
    public function identify(?Request $request = null, ?string $identifier = null): string;

    public function key(): string;
}
