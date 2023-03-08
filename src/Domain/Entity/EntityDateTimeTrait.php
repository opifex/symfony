<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\Entity\EntityInterface;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

trait EntityDateTimeTrait
{
    #[Groups([EntityInterface::GROUP_INDEX, EntityInterface::GROUP_VIEW])]
    protected ?DateTimeImmutable $createdAt = null;

    #[Groups([EntityInterface::GROUP_INDEX, EntityInterface::GROUP_VIEW])]
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
