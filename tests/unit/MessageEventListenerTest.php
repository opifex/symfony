<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\MessageEventListener;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageEventListenerTest extends Unit
{
    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        $this->envelope = $this->createMock(originalClassName: Envelope::class);
        $this->rawMessage = $this->createMock(originalClassName: NotificationEmail::class);
        $this->translator = $this->createMock(originalClassName: TranslatorInterface::class);
    }

    public function testInvokeWithNotificationEmail(): void
    {
        $messageListener = new MessageEventListener($this->translator);
        $messageEvent = new MessageEvent($this->rawMessage, $this->envelope, transport: 'email');
        ($messageListener)($messageEvent);

        $this->expectNotToPerformAssertions();
    }
}
