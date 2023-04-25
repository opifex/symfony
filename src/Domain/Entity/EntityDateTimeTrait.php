<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

trait EntityDateTimeTrait
{
    protected ?DateTimeImmutable $createdAt = null;

    protected ?DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function prePersistDateTime(): void
    {
        $datetime = new DateTimeImmutable();
        $this->createdAt = $datetime;
        $this->updatedAt = $datetime;
    }

    public function preUpdateDateTime(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
