<?php

declare(strict_types=1);

namespace App\Application\Contract;

use SensitiveParameter;

interface UserPasswordHasherInterface
{
    public function hash(#[SensitiveParameter] string $plainPassword): string;
}
