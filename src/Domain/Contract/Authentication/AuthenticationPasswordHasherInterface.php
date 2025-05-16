<?php

declare(strict_types=1);

namespace App\Domain\Contract\Authentication;

use SensitiveParameter;

interface AuthenticationPasswordHasherInterface
{
    public function hash(#[SensitiveParameter] string $plainPassword): string;
}
