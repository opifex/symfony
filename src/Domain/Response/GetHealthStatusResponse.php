<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;
use Symfony\Component\Serializer\Annotation\Groups;

final class GetHealthStatusResponse
{
    final public const GROUP_VIEW = __CLASS__ . ':view';

    #[Groups(self::GROUP_VIEW)]
    public readonly HealthStatus $status;

    public function __construct(Health $health)
    {
        $this->status = $health->getStatus();
    }
}
