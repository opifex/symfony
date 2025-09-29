<?php

declare(strict_types=1);

namespace App\Application\Contract;

use SensitiveParameter;

interface AuthenticationPasswordHasherInterface
{
    public function hash(#[SensitiveParameter] string $plainPassword): string;
}
