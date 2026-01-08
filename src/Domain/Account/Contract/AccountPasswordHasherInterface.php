<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use SensitiveParameter;

interface AccountPasswordHasherInterface
{
    public function hash(#[SensitiveParameter] string $plainPassword): string;
}
