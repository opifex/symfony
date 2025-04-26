<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Health
{
    public function __construct(
        private readonly string $status,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
