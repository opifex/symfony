<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum HealthStatus: string
{
    case Ok = 'ok';

    public function toString(): string
    {
        return $this->value;
    }
}
