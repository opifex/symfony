<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\EventListener\AccountWorkflowEventListener;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountWorkflowEventListenerTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;

    #[Override]
    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(type: EventDispatcherInterface::class);
    }

    public function testOnWorkflowAccountCompletedRegister(): void
    {
        $accountWorkflowEventListener = new AccountWorkflowEventListener(
            eventDispatcher: $this->eventDispatcher,
        );

        $this->expectException(InvalidArgumentException::class);

        $accountWorkflowEventListener->onWorkflowAccountCompletedRegister(
            event: new CompletedEvent(new stdClass(), new Marking()),
        );
    }
}
