<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AccountEntityBuilderInterface
{
    public function build(string $email, string $hashedPassword, string $locale): AccountEntityInterface;
}
