<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

final class AccountMarking
{
    public function __construct(
        public string $marking,
    ) {
    }
}
