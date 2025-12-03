<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Workflow\AccountWorkflowListener;
use Override;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountWorkflowListenerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(type: EventDispatcherInterface::class);
    }

    public function testOnRegisterCompleted(): void
    {
        $accountWorkflowListener = new AccountWorkflowListener(
            eventDispatcher: $this->eventDispatcher,
        );

        $this->expectException(InvalidArgumentException::class);

        $accountWorkflowListener->onRegisterCompleted(
            event: new CompletedEvent(new stdClass(), new Marking()),
        );
    }
}
