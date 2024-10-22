<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface ActivatedUserInterface
{
    public function isActivated(): bool;
}
