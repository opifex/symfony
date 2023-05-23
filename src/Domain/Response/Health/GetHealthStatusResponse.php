<?php

declare(strict_types=1);

namespace App\Domain\Response\Health;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;
use Symfony\Component\Serializer\Annotation\Groups;

final class GetHealthStatusResponse
{
    public const GROUP_VIEW = __CLASS__ . ':view';

    #[Groups(self::GROUP_VIEW)]
    public readonly HealthStatus $status;

    public function __construct(Health $health)
    {
        $this->status = $health->getStatus();
    }
}
