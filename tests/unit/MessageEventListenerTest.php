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
        $translator = $this->createMock(originalClassName: TranslatorInterface::class);
        $this->messageListener = new MessageEventListener($translator);
    }

    /**
     * @throws MockObjectException
     */
    public function testInvokeWithNotificationEmail(): void
    {
        $rawMessage = $this->createMock(originalClassName: NotificationEmail::class);
        $envelope = $this->createMock(originalClassName: Envelope::class);

        ($this->messageListener)(new MessageEvent($rawMessage, envelope: $envelope, transport: 'email'));
    }
}
