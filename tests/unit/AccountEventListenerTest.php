<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\AccountEventListener;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountEventListenerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->notifier = $this->createMock(originalClassName: NotifierInterface::class);
        $this->translator = $this->createMock(originalClassName: TranslatorInterface::class);
    }

    public function testOnWorkflowAccountCompletedActivateWithInvalidObjectReturnNothing(): void
    {
        $accountEventListener = new AccountEventListener($this->notifier, $this->translator);

        $this->notifier
            ->expects($this->never())
            ->method(constraint: 'send');

        $accountEventListener->onWorkflowAccountCompletedActivate(new CompletedEvent(new stdClass(), new Marking()));
    }
}
