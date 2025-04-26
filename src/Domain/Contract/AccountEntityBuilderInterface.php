<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AccountEntityBuilderInterface
{
    public function withEmail(string $email): self;

    public function withPassword(string $hashedPassword): self;

    public function withLocale(string $locale): self;

    public function build(): AccountEntityInterface;
}
