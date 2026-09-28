<?php

declare(strict_types=1);

namespace App\Domain\Foundation\Enum;

enum HealthStatus: string
{
    case Ok = 'ok';

    public function toString(): string
    {
        return $this->value;
    }
}
