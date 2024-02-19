<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use SensitiveParameter;

interface AccountPasswordHasherInterface
{
    public function hash(#[SensitiveParameter] string $password): string;
}
