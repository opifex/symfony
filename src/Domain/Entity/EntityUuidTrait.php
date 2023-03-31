<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\Entity\EntityInterface;
use Symfony\Component\Serializer\Annotation\Groups;

trait EntityUuidTrait
{
    #[Groups([EntityInterface::GROUP_INDEX, EntityInterface::GROUP_VIEW])]
    protected ?string $uuid = null;

    public function getUuid(): string
    {
        return $this->uuid ?? '';
    }
}
