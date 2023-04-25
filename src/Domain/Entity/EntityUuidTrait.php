<?php

declare(strict_types=1);

namespace App\Domain\Entity;

trait EntityUuidTrait
{
    protected ?string $uuid = null;

    public function getUuid(): string
    {
        return $this->uuid ?? '';
    }
}
