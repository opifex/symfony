<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class HealthStatus
{
    public const string OK = 'ok';
    // List of all available health statuses
    public const array CASES = [
        self::OK,
    ];
}
