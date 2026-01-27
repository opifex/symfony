<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Presentation\Scheduler\SymfonyCronTask;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[AllowDynamicProperties]
final class SymfonyCronTaskTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->logger = $this->createMock(type: LoggerInterface::class);
    }

    public function testInvokeCronTask(): void
    {
        new SymfonyCronTask($this->logger)();

        $this->expectNotToPerformAssertions();
    }
}
