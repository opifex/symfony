<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;
use Codeception\Test\Unit;

final class HealthTest extends Unit
{
    public function testGetStatus(): void
    {
        $health = new Health(status: HealthStatus::OK);

        $this->assertEquals(expected: HealthStatus::OK, actual: $health->getStatus());
    }
}
