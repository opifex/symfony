<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\EventListener\AccountWorkflowEventListener;
use App\Domain\Contract\AccountRepositoryInterface;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountWorkflowEventListenerTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(type: AccountRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(type: EventDispatcherInterface::class);
    }

    public function testOnWorkflowAccountCompletedRegister(): void
    {
        $accountWorkflowEventListener = new AccountWorkflowEventListener(
            accountRepository: $this->accountRepository,
            eventDispatcher: $this->eventDispatcher,
        );

        $this->expectException(InvalidArgumentException::class);

        $accountWorkflowEventListener->onWorkflowAccountCompletedRegister(
            event: new CompletedEvent(new stdClass(), new Marking()),
        );
    }
}
