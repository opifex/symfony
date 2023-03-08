<?php

declare(strict_types=1);

namespace App\Domain\Entity\Health;

use App\Domain\Contract\Entity\EntityInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class Health implements EntityInterface
{
    #[Groups([self::GROUP_VIEW])]
    protected HealthStatus $status;

    public function __construct(HealthStatus $status)
    {
        $this->status = $status;
    }

    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
