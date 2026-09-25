<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Messenger\FailedMessageCounter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

final class FailedMessageCounterTest extends TestCase
{
    public function testCountReturnsTransportMessageCount(): void
    {
        $messageCountAware = $this->createStub(type: MessageCountAwareInterface::class);
        $messageCountAware->method(constraint: 'getMessageCount')->willReturn(value: 7);

        $failedMessageCounter = new FailedMessageCounter($messageCountAware);

        $this->assertSame(expected: 7, actual: $failedMessageCounter->count());
    }
}
